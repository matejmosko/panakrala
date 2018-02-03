<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__."/kamosko-config.php");
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/db.php');
require __DIR__ . '/vendor/autoload.php';

$data = getData();
$loader = new Twig_Loader_Filesystem($GLOBALS['options']['basepath'].'templates/');
$twig = new Twig_Environment($loader);
saveFiles();
/*
    echo "<pre>";
    print_r($data);
    echo "</pre>";
*/
echo "<pre>";
print_r($data);
echo "</pre>";
/*
function filterEvents($data, $project, $filter)
{
    $today = time();
    if (array_key_exists('events', $data['projects'][$project])) {
        switch ($filter) {
      case 'all':
        return $data;
        break;
      case 'next':
        foreach ($data['projects'][$project]['events'] as $key => &$event) {
            if (array_key_exists('opts', $event) && $event['opts']['timestamp'] <= $today) {
                unset($data['projects'][$project]['events'][$key]);
            }
        }
        return $data;
        break;
      case 'past':
        foreach ($data['projects'][$project]['events'] as $key => &$event) {
            if (array_key_exists('opts', $event) && $event['opts']['timestamp'] >= $today) {
                unset($data['projects'][$project]['events'][$key]);
            }
        }
        return $data;
        break;
      default:
        return $data;
        break;
    }
    } else {
        "K tomuto projektu momentálne nepripravujeme žiadne verejné udalosti.";
    }
}*/

function renderForm($projectId, $eventId)
{
    return $GLOBALS['twig']->render('registration.html', array(
    'projectId' => $projectId,
    'eventId' => $eventId,
    'project' => $GLOBALS['data']['projects'][$projectId],
    'event' => $GLOBALS['data']['projects'][$projectId]['events'][$eventId],
    'options' => $GLOBALS['options'],
    'guestCount' => eventGuestCount($eventId),
    'recaptcha' => "recaptcha" // TODO Recaptcha
  ));
}

function renderEvent($projectId, $eventId)
{
    return $GLOBALS['twig']->render('event.html', array(
    'project' => $GLOBALS['data']['projects'][$projectId],
    'projectId' => $projectId,
    'eventId' => $eventId,
    'event' => $GLOBALS['data']['projects'][$projectId]['events'][$eventId],
    'form' => renderForm($projectId, $eventId),
    'options' => $GLOBALS['options'],
    'guestCount' => eventGuestCount($eventId),
    'guestList' => eventGetGuests($eventId)
  ));
}

function renderEvents($projectId, $filter)
{
    $today = $GLOBALS['options']['today'];
    $html = '<div class="events flexRow">';

    // echo "<pre>"; print_r($GLOBALS['data']['projects']); echo "</pre>";

    if (array_key_exists('events', $GLOBALS['data']['projects'][$projectId])) {
        switch ($filter) {
  case 'all':
  foreach ($GLOBALS['data']['projects'][$projectId]['events'] as $key => &$event) {
      $html .= renderEvent($projectId, $key, $today);
  }
    break;
  case 'next':
    foreach ($GLOBALS['data']['projects'][$projectId]['events'] as $key => &$event) {
        if (array_key_exists('opts', $event) && $event['opts']['timestamp'] >= $today) {
            $html .= renderEvent($projectId, $key, $today);
        }
    }
    break;
  case 'past':
    foreach ($GLOBALS['data']['projects'][$projectId]['events'] as $key => &$event) {
        if (array_key_exists('opts', $event) && $event['opts']['timestamp'] <= $today) {
            $html .= renderEvent($projectId, $key, $today);
        }
    }
    break;
  default:
    break;
}
    } else {
        $html .= "K tomuto projektu momentálne nepripravujeme žiadne verejné udalosti.";
    }

    $html .= '</div>';
    return $html;
}

function renderProjects()
{
    return $GLOBALS['twig']->render('projectlist.html', array(
    'projects' => $GLOBALS['data']['projects'],
    'options' => $GLOBALS['options']
  ));
}

function renderCover()
{
    return $GLOBALS['twig']->render('cover.html', array(
    'data' => $GLOBALS['data'],
    'options' => $GLOBALS['options']
  ));
}
function renderFooter()
{
    return $GLOBALS['twig']->render('footer.html', array(
    'data' => $GLOBALS['data'],
    'options' => $GLOBALS['options']
  ));
}

function renderHead()
{
    return $GLOBALS['twig']->render('head.html', array(
    'data' => $GLOBALS['data'],
    'options' => $GLOBALS['options']
  ));
}

function renderScripts()
{
    return $GLOBALS['twig']->render('scripts.html', array(
    'data' => $GLOBALS['data'],
    'options' => $GLOBALS['options']
  ));
}

function renderProject($projectId)
{
    return $GLOBALS['twig']->render('project.html', array(
    'data' => $GLOBALS['data']['projects'][$projectId],
    'project' => $projectId,
    'nextEvents' => renderEvents($projectId, 'next'),
    'options' => $GLOBALS['options']
    //'pastEvents' => renderEvents($project, 'past')
  ));
}

function renderMenu()
{
    return $GLOBALS['twig']->render('menu.html', array(
    'menu' => $GLOBALS['data']['opts']['menu'],
    'options' => $GLOBALS['options']
  ));
}

function renderGallery($projectId, $gallery)
{
    return $GLOBALS['twig']->render('gallery.html', array(
    'data' => $GLOBALS['data']['projects'][$projectId]['gallery'][$gallery],
    'project' => $projectId,
    'gallery' => $gallery,
    'options' => $GLOBALS['options']
  ));
}

function renderHomePage()
{
    return $GLOBALS['twig']->render('page.html', array(
  'menu' => renderMenu(),
  'head' => renderHead(),
  'cover' => renderCover(),
  'content' => renderProjects(),
  'footer' => renderFooter(),
  'scripts' => renderScripts(),
  'masterClass' => 'home',
  'options' => $GLOBALS['options']
));
}

function renderProjectPage($projectId)
{
    return $GLOBALS['twig']->render('page.html', array(
  'menu' => renderMenu(),
  'head' => renderHead(),
  'cover' => renderCover(),
  'content' => renderProject($projectId),
  //'footer' => renderFooter(),
  'scripts' => renderScripts(),
  'masterClass' => 'project',
  'options' => $GLOBALS['options']
));
}

function renderGalleryPage($projectId, $gallery)
{
    return $GLOBALS['twig']->render('page.html', array(
  'menu' => renderMenu(),
  'head' => renderHead(),
  'cover' => renderCover(),
  'content' => renderGallery($projectId, $gallery),
  //'footer' => renderFooter(),
  'scripts' => renderScripts(),
  'masterClass' => 'project',
  'options' => $GLOBALS['options']
));
}

function renderDocumentPage($document)
{
    return $GLOBALS['twig']->render('page.html', array(
      'menu' => renderMenu(),
      'head' => renderHead(),
      'cover' => renderCover(),
      'content' => $document,
      'footer' => renderFooter(),
      'scripts' => renderScripts(),
      'masterClass' => 'home',
      'options' => $GLOBALS['options']
  ));
}

function saveFiles()
{
    /* Save Index.html */
    $content = renderHomePage();
    file_put_contents($GLOBALS['options']['basepath'].'index.html', $content);
    /* Save Projects' html files */
    foreach ($GLOBALS['data']['projects'] as $key => $project) {
        $dir = $GLOBALS['options']['basepath'].'./'.$key.'/';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($dir.'/index.html', renderProjectPage($key));
        if (array_key_exists('gallery', $project)) {
            foreach ($project['gallery'] as $key2 => $gallery) {
                $subdir = $GLOBALS['options']['basepath'].'./'.$key.'/gallery/';
                if (!file_exists($subdir)) {
                    mkdir($subdir, 0777, true);
                }
                file_put_contents($subdir.'/'.$key2.'.html', renderGalleryPage($key, $key2));
            }
        }
    }
    /* Save Documents' html files */
    foreach ($GLOBALS['data']['documents'] as $key => $doc) {
        file_put_contents($GLOBALS['options']['basepath'].pathinfo($doc, PATHINFO_FILENAME).'.html', renderDocumentPage(file_get_contents($GLOBALS['options']['basepath']."data/documents/".$doc)));
    }
}




/*

You can create your own function or test and just pass the arguments to the PHP function.

$test = new Twig_SimpleTest('ondisk', function ($file) {
    return file_exists($file);
});

And then in your template:

{% if filename is ondisk %}
    blah
{% endif %}

Unfortunately is exists sounds weird in English. Perhaps a function would make more sense.


*/