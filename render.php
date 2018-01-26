<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('functions.php');
require_once('lib/Twig/Autoloader.php');
 Twig_Autoloader::register();

$data = getData();
$baseUrl = 'http://localhost/~gnaag/panakrala/';

    $loader = new Twig_Loader_Filesystem('templates/');
    $twig = new Twig_Environment($loader);
saveFiles($data);
/*
    echo "<pre>";
    print_r($data);
    echo "</pre>";
*/
echo "<pre>";
print_r($data);
echo "</pre>";
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
}

function renderForm($project, $event){
  return $GLOBALS['twig']->render('registration.html', array(
    'project' => $project,
    'event' => $event
  ));
}

function renderEvent($project, $event, $today){
  return $GLOBALS['twig']->render('event.html', array(
    'project' => $project,
    'event' => $event,
    'today' => $today,
    'form' => renderForm($project, $event),
  ));
}

function renderEvents($localData, $project, $filter)
{
$today = time();
$html = '<div class="events flexRow">';
if (array_key_exists('events', $localData['projects'][$project])) {
    switch ($filter) {
  case 'all':
  foreach ($localData['projects'][$project]['events'] as $key => &$event) {
        $html .= renderEvent($project, $event, $today);
  }
    break;
  case 'next':
    foreach ($localData['projects'][$project]['events'] as $key => &$event) {
        if (array_key_exists('opts', $event) && $event['opts']['timestamp'] >= $today) {
          $html .= renderEvent($project, $event, $today);
          echo "wox";
        }
    }
    break;
  case 'past':
    foreach ($localData['projects'][$project]['events'] as $key => &$event) {
        if (array_key_exists('opts', $event) && $event['opts']['timestamp'] <= $today) {
            $html .= renderEvent($project, $event, $today);
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
/*    $today = time();
    $localData = filterEvents($localData, $project, $filter);

    return $GLOBALS['twig']->render('eventlist.html', array(
      'project' => $localData['projects'][$project],
      'path' => $project,
      'today' => $today,
      'filter' => $filter /* all, next, past
    ));*/
}

function renderProjects($localData)
{
    return $GLOBALS['twig']->render('projectlist.html', array(
    'projects' => $localData['projects']
  ));
}

function renderCover($localData)
{
    return $GLOBALS['twig']->render('cover.html', array(
    'data' => $localData
  ));
}
function renderFooter($localData)
{
    return $GLOBALS['twig']->render('footer.html', array(
    'data' => $localData
  ));
}

function renderHead($localData)
{
    return $GLOBALS['twig']->render('head.html', array(
    'data' => $localData
  ));
}

function renderScripts($localData)
{
    return $GLOBALS['twig']->render('scripts.html', array(
    'data' => $localData
  ));
}

function renderProject($localData, $project)
{
    return $GLOBALS['twig']->render('project.html', array(
    'data' => $localData['projects'][$project],
    'project' => $project,
    'nextEvents' => renderEvents($localData, $project, 'next')
    //'pastEvents' => renderEvents($localData, $project, 'past')
  ));
}

function renderMenu($localData)
{
    return $GLOBALS['twig']->render('menu.html', array(
    'menu' => $localData['opts']['menu']
  ));
}

function renderGallery($localData, $project, $gallery)
{
    return $GLOBALS['twig']->render('gallery.html', array(
    'data' => $localData['projects'][$project]['gallery'][$gallery],
    'project' => $project,
    'gallery' => $gallery,
    'baseUrl' => $GLOBALS['baseUrl']
  ));
}

function renderHomePage($localData)
{
    return $GLOBALS['twig']->render('page.html', array(
  'menu' => renderMenu($localData),
  'head' => renderHead($localData),
  'cover' => renderCover($localData),
  'content' => renderProjects($localData),
  'footer' => renderFooter($localData),
  'scripts' => renderScripts($localData),
  'masterClass' => 'home'
));
}

function renderProjectPage($localData, $project)
{
    return $GLOBALS['twig']->render('page.html', array(
  'menu' => renderMenu($localData),
  'head' => renderHead($localData),
  'cover' => renderCover($localData),
  'content' => renderProject($localData, $project),
  //'footer' => renderFooter($localData),
  'scripts' => renderScripts($localData),
  'masterClass' => 'project'
));
}

function renderGalleryPage($localData, $project, $gallery)
{
    return $GLOBALS['twig']->render('page.html', array(
  'menu' => renderMenu($localData),
  'head' => renderHead($localData),
  'cover' => renderCover($localData),
  'content' => renderGallery($localData, $project, $gallery),
  //'footer' => renderFooter($localData),
  'scripts' => renderScripts($localData),
  'masterClass' => 'project'
));
}

function renderDocumentPage($localData, $document)
{
    return $GLOBALS['twig']->render('page.html', array(
      'menu' => renderMenu($localData),
      'head' => renderHead($localData),
      'cover' => renderCover($localData),
      'content' => $document,
      'footer' => renderFooter($localData),
      'scripts' => renderScripts($localData),
      'masterClass' => 'home'
  ));
}

function saveFiles($localData)
{
    /* Save Index.html */
    $content = renderHomePage($localData);
    file_put_contents('index.html', $content);
    /* Save Projects' html files */
    foreach ($localData['projects'] as $key => $project) {
        file_put_contents($key.'.html', renderProjectPage($localData, $key));
        if (array_key_exists('gallery', $project)) {
            foreach ($project['gallery'] as $key2 => $gallery) {
                $dir = './'.$key.'/gallery/';
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                file_put_contents($dir.'/'.$key2.'.html', renderGalleryPage($localData, $key, $key2));
            }
        }
    }
    foreach ($localData['documents'] as $key => $doc) {
        file_put_contents(pathinfo($doc, PATHINFO_FILENAME).'.html', renderDocumentPage($localData, file_get_contents("data/documents/".$doc)));
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
