<?php

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
    $data = recursiveScandir($GLOBALS['options']['basepath'].'/data');
    $data = recursiveJsonSearch($data, $GLOBALS['options']['basepath'].'/data');
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

function recursiveJsonSearch(&$data, $path)
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

function render_new_google_form($url, $formid, $formtitle, $height)
{
    $content .= '<iframe id="google-form" src="'.$url.'" width="760" height="'.$height.'" frameborder="0" marginheight="0" marginwidth="0" style="display:none">'.$strings['loading'].'</iframe>';
    if (!empty(filter_events('next'))) {
        echo $content;
    };
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
