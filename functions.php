<?php
/**
 * Get a web file (HTML, XHTML, XML, image, etc.) from a Google Forms.  Return an array containing the HTTP server response header fields and content.
 */
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
/** CALLS **/

  if (isset($_GET['script'])) {
      $script = $_GET['script'];
      switch ($script) {
      case 'getData':
        echo json_encode(getData(), JSON_UNESCAPED_UNICODE);
        json_encode(getData(), JSON_UNESCAPED_UNICODE);
      break;
      case 'getProjects':
        echo json_encode(getProjects(), JSON_UNESCAPED_UNICODE);
        break;
      case 'getEvents':
        if (isset($_GET['project'])) {
            echo json_encode(getProject($_GET['project']), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(getProjects(), JSON_UNESCAPED_UNICODE);
        }
        break;
      case 'getEvent':
        if (isset($_GET['event']) && isset($_GET['project'])) {
            echo json_encode(getEvent($_GET['project'], $_GET['event']), JSON_UNESCAPED_UNICODE);
        } else {
            echo "{}";
        }
          break;
          case 'getPartners':
            echo json_encode(getPartners(), JSON_UNESCAPED_UNICODE);
          break;
      default:
        # code...
        break;
    }
  }

// TODO After each reload save json data for initial loading (cache).

/** FUNCTIONS **/


function getData()
{
    $data = recursiveScandir('data');
    $data = recursiveJsonSearch($data);
    //$data['projects'] = parseProjectFiles($data['projects']);
    return $data;
}

function getProjects()
{
    $data = recursiveScandir('data/projects/');
    $data = parseInfoFiles($data);
    return $data;
}
function getProject($project)
{
    $data = recursiveScandir('data/projects/'.$project);
    return $data;
}

function getEvent($project, $event)
{
    return recursiveScandir('data/projects/'.$project."/events/".$event);
}

function getPartners()
{
    $partners = recursiveScandir("data/partners");
    return $partners;
}

function recursiveScandir($path)
{
    $files = array();
    $entries = scandir($path);
    foreach ($entries as $entry) {
        if ($entry != "." && $entry != "..") { //Just removed . and ..
            if (is_dir($path . DIRECTORY_SEPARATOR . $entry)) {
                $files[$entry] = recursiveScandir($path . DIRECTORY_SEPARATOR . $entry);
            } else {
                $files[] = $entry;
            }
        }
    }
    return $files;
}

function parseProjectFiles($projects)
{
    foreach ($projects as $key => $project) {
        $myfile = "data/projects/".$key."/info.json";
        if (file_exists($myfile)) {
            $infodata = file_get_contents($myfile);
        } else {
            $infodata = [];
        }
        $projects[$key]['info'] = json_decode($infodata, true);
    }
    return $projects;
}

function parseJsonFile($value, $path)
{
    $myfile = $path."/".$value;
    /*echo $myfile;
    print_r(explode("/", $path));
    echo "<br />";*/
    if (file_exists($myfile)) {
        $infodata = file_get_contents($myfile);
    } else {
        $infodata = [];
    }
    return $infodata;
}

function recursiveJsonSearch(&$data, $path = "data")
{
    foreach ($data as $key => &$value) {
        if (is_object($value) or is_array($value)) {
            recursiveJsonSearch($value, $path."/".$key);
        } elseif (pathinfo($value, PATHINFO_EXTENSION) == "json" && $value != "registration.json") {

            $json = parseJsonFile($value, $path);
            $n = explode("/", $path);

            $data['opts'] = json_decode($json, true);
            /* Convert date to timestamp for later comparison */
            if (array_key_exists('date', $data['opts'])) {
                $data['opts']['timestamp'] = strtotime($data['opts']['date']);
            }
        } /*elseif (pathinfo($value, PATHINFO_EXTENSION) == "md") {
            //$json = parseJsonFile($value, $path);
            $n = explode("/", $path);

            $data[$value] = file_get_contents($path."/".$value);
        }*/
    }
    return $data;
}

/* REGISTRATION SYSTEM */

function showGuests(){

}

/*
function get_cover_image()
{
    if (isset($_GET['p'])) {
        $p = $_GET['p'];
        echo '/img/cover/'.$p.'.jpg';
    } else {
        echo '/'.next_event_pic();
    }
}

/*
function next_event_pic()
{
    $events = filter_events('next');
    if (!empty($events)) {
        return 'events/'.$events[0].'/img.jpg';
    } else {
        return 'img/cover/default-cover.jpg';
    }
}

function render_new_google_form($url, $formid, $formtitle, $height)
{
    $content .= '<iframe id="google-form" src="'.$url.'" width="760" height="'.$height.'" frameborder="0" marginheight="0" marginwidth="0" style="display:none">'.$strings['loading'].'</iframe>';
    if (!empty(filter_events('next'))) {
        echo $content;
    };
}

function render_menu($menuitems)
{
    $result = '<span id="menu-button"><a href="#menu"></a></span>';
    $result .= '<ul id="menu">';
    foreach ($menuitems as $item) {
        $result .= '<li><a href="'.$item["link"].'" title="'.$item["desc"].'">'.$item["name"].'</a></li>';
    }
    $result .= '</ul>';
    echo $result;
}
function render_file($page)
{
    //if (isset($_GET['page'])) $page = $_GET['page'];
    switch (true) {
      case strpos($page, '.php'):
          include_once($page);
          break;
      case strpos($page, '.md'):
          $markdown = file_get_contents($page);
          $Parsedown = new ParsedownExtra();
          echo $Parsedown->text($markdown);
          break;
  }
}

function filter_events($filter)
{
    $next_events = array();
    $past_events = array();
    $list_events = scandir('events/');
    foreach (array_slice($list_events, 2) as $event) {
        $date = substr($event, 0, 10);
        //echo $date.'<br />';
        $today = strtotime(date('Y-m-d'));
        if (strtotime($date) >= $today) {
            $next_events[] = $event;
        }
        if (strtotime($date) < $today) {
            $past_events[] = $event;
        }
    }
    if ($filter == 'next') {
        return $next_events;
    }
    if ($filter == 'past') {
        return $past_events;
    }
}

function render_events($filter, $render_md, $sort)
{
    global $strings;
    $events = filter_events($filter);
    if ($sort == 'desc') {
        rsort($events);
    }
    if ($sort == 'asc') {
        sort($events);
    }
    if (!empty($events) && $filter == 'next') {
        echo '<h2 id="najblizsia-hra">'.$strings['events_next'].'</h2>';
    }
    if (!empty($events) && $filter == 'past') {
        echo '<h2 id="najblizsia-hra">'.$strings['events_past'].'</h2>';
    }
    foreach ($events as $event) {
        $path = 'events/'.$event.'/';
        echo '<div class="event">';
        if (file_exists($path.'img.jpg')) {
            echo '<a data-fancybox="events" title="'.basename($path).'" href="'.$path.'/img.jpg"><img src="'.$path.'img.jpg" /></a>';
        }
        if (file_exists($path.'event.md') && $render_md) {
            render_file($path.'event.md');
        }
        if (!empty($events) && $filter == 'next') {
            echo '<p><a class="op-button" data-fancybox="registracia" data-src="#google-form" href="javascript:;">'.$strings['register_to_event'].'</a></p>';
        }
        echo '</div>';
    }
}

function render_picfolder($path, $clickable, $maxwidth)
{
    if (file_exists($path.'/desc.md')) {
        render_file($path.'/desc.md');
    }
    if (!$maxwidth){ $maxwidth = 'none';}
    $pics = array_diff(scandir($path), array('..', '.','orig','desc.md'));
    natsort($pics);
    $pics = array_reverse($pics, true);
    foreach ($pics as $picture) {
        $picname = preg_replace('/\\.[^.\\s]{3,4}$/', '', $picture);
        if ($clickable) {echo '<p class="archive" style="max-width:'.$maxwidth.'"><a data-fancybox="picfolder" title="'.$picname.'" href="'.$path.'/'.$picture.'"><img src="'.$path.'/'.$picture.'" /></a></p>';}
        else {echo '<p class="archive" style="max-width:'.$maxwidth.'"><img src="'.$path.'/'.$picture.'" /></p>';}
    }
}*/
