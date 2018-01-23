<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('functions.php');
require_once('lib/Twig/Autoloader.php');
 Twig_Autoloader::register();

$data = getData();

    $loader = new Twig_Loader_Filesystem('templates/');
    $twig = new Twig_Environment($loader);
saveFiles($data, $twig);
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
  if (array_key_exists('events', $data['projects'][$project])){
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
  } else {"K tomuto projektu momentálne nepripravujeme žiadne verejné udalosti.";}
}

function renderEvents($localData, $project, $twig, $filter)
{
    $today = time();
    $localData = filterEvents($localData, $project, $filter);

    return $twig->render('eventlist.html', array(
      'project' => $localData['projects'][$project],
      'path' => $project,
      'today' => $today,
      'filter' => $filter /* all, next, past */
    ));
}

function renderProjects($localData, $twig)
{
    return $twig->render('projectlist.html', array(
    'projects' => $localData['projects']
  ));
}

function renderCover($localData, $twig)
{
    return $twig->render('cover.html', array(
    'data' => $localData
  ));
}
function renderFooter($localData, $twig)
{
    return $twig->render('footer.html', array(
    'data' => $localData
  ));
}

function renderHead($localData, $twig)
{
    return $twig->render('head.html', array(
    'data' => $localData
  ));
}

function renderScripts($localData, $twig)
{
    return $twig->render('scripts.html', array(
    'data' => $localData
  ));
}

function renderProject($localData, $project, $twig)
{
    return $twig->render('project.html', array(
    'data' => $localData['projects'][$project],
    'project' => $project,
    'nextEvents' => renderEvents($localData, $project, $twig, 'next')
    //'pastEvents' => renderEvents($localData, $project, $twig, 'past')
  ));
}

function renderMenu($localData, $twig)
{
    return $twig->render('menu.html', array(
    'menu' => $localData['opts']['menu']
  ));
}

function renderHomePage($localData, $twig)
{
  return $twig->render('page.html', array(
  'menu' => renderMenu($localData, $twig),
  'head' => renderHead($localData, $twig),
  'cover' => renderCover($localData, $twig),
  'content' => renderProjects($localData, $twig),
  'footer' => renderFooter($localData, $twig),
  'scripts' => renderScripts($localData, $twig),
  'masterClass' => 'home'
));
}

function renderProjectPage($localData, $project, $twig)
{
  return $twig->render('page.html', array(
  'menu' => renderMenu($localData, $twig),
  'head' => renderHead($localData, $twig),
  'cover' => renderCover($localData, $twig),
  'content' => renderProject($localData, $project, $twig),
  //'footer' => renderFooter($localData, $twig),
  'scripts' => renderScripts($localData, $twig),
  'masterClass' => 'project'
));
}

function renderDocumentPage($localData, $document, $twig)
{
    return $twig->render('page.html', array(
      'menu' => renderMenu($localData, $twig),
      'head' => renderHead($localData, $twig),
      'cover' => renderCover($localData, $twig),
      'content' => $document,
      'footer' => renderFooter($localData, $twig),
      'scripts' => renderScripts($localData, $twig),
      'masterClass' => 'home'
  ));
}

function saveFiles($localData, $twig){
  /* Save Index.html */
  $content = renderHomePage($localData, $twig);
  file_put_contents('index.html',$content);
  /* Save Projects' html files */
  foreach ($localData['projects'] as $key => $project){
    file_put_contents($key.'.html',renderProjectPage($localData, $key, $twig));
  }
  foreach ($localData['documents'] as $key => $doc){
    file_put_contents(pathinfo($doc, PATHINFO_FILENAME).'.html',renderDocumentPage($localData, file_get_contents("data/documents/".$doc), $twig));
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
