<?php
$debug = $_GET['debug'];

if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

Locale::setDefault('sk_SK');



require_once(__DIR__ . '/functions.php');

$data = getData();

$loader = new Twig_Loader_Filesystem($GLOBALS['options']['basepath'].'templates/');
$twig = new Twig_Environment($loader);
$twig->addExtension(new Twig_Extensions_Extension_Intl());

saveFiles();

if ($debug) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}
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
    return $GLOBALS['twig']->render('registration-'.$projectId.'.html', array(
    'projectId' => $projectId,
    'eventId' => $eventId,
    'project' => $GLOBALS['data']['projects'][$projectId],
    'event' => $GLOBALS['data']['projects'][$projectId]['events'][$eventId],
    'options' => $GLOBALS['options'],
    'checkboxes' => renderFormCheckboxes(),
    'guestCount' => eventGuestCount($eventId),
    'recaptcha' => "recaptcha" // TODO Recaptcha
  ));
}

function renderEvent($projectId, $eventId)
{
    $path = $GLOBALS['options']['baseurl']."/data/projects/".$projectId."/events/";
    if (file_exists($path.$eventId."/cover.jpg")) {
        $eventPic = $path.$eventId."/cover.jpg";
    } else {
        $eventPic = $path."/default.jpg";
    }
$form = "";
if ($GLOBALS['data']['projects'][$projectId]['events'][$eventId]['opts']['regLink'] == ""){
$form = renderForm($projectId, $eventId);
}

    return $GLOBALS['twig']->render('event.html', array(
    'project' => $GLOBALS['data']['projects'][$projectId],
    'projectId' => $projectId,
    'eventId' => $eventId,
    'eventPic' => $eventPic,
    'event' => $GLOBALS['data']['projects'][$projectId]['events'][$eventId],
    'form' => $form,
    'options' => $GLOBALS['options'],
    'guestCount' => eventGuestCount($eventId),
    'guestList' => eventGetGuests($projectId, $eventId)
  ));
}

function renderEvents($projectId, $filter)
{
    $today = $GLOBALS['options']['today'];
    $html = '';

    if (array_key_exists('events', $GLOBALS['data']['projects'][$projectId])) {
        foreach ($GLOBALS['data']['projects'][$projectId]['events'] as $key => &$event) {
            if (is_string($event)) { // Checks if $event is array (folder) or simple file.
                break;
            }
            switch ($filter) {
  case 'all':
    $html .= renderEvent($projectId, $key, $today);
    break;
  case 'next':
    if (array_key_exists('opts', $event) && $event['opts']['timestamp'] >= $today) {
        $html .= renderEvent($projectId, $key, $today);
    }
    break;
  case 'past':
    if (array_key_exists('opts', $event) && $event['opts']['timestamp'] <= $today) {
        $html .= renderEvent($projectId, $key, $today);
    }
    break;
  default:
    break;
          }
        }
    } else {
        $html .= "Momentálne nepripravujeme žiadne verejné udalosti.";
    }

    return $html;
}

function renderProjects()
{
    return $GLOBALS['twig']->render('projectlist.html', array(
    'projects' => $GLOBALS['data']['projects'],
    'options' => $GLOBALS['options']
  ));
}

function renderCover($masterClass = null, $projectId = null, $eventId = null)
{
    return $GLOBALS['twig']->render('cover.html', array(
    'data' => $GLOBALS['data'],
    'options' => $GLOBALS['options'],
    'masterClass' => $masterClass,
    'projectId' => $projectId,
    'eventId' => $eventId
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
    'projectId' => $projectId,
    'nextEvents' => renderEvents($projectId, 'next'),
    'options' => $GLOBALS['options'],
    'contactForm' => renderContactForm('Záujem o '.$projectId, 'Dobrý deň, máme záujem o váš produkt'.$projectId.'. Pošlite nám prosím detailnejšie informácie.')
    //'pastEvents' => renderEvents($project, 'past')
  ));
}

function renderDocument($document)
{
    $image = pathinfo($document, PATHINFO_FILENAME).".jpg";
    return $GLOBALS['twig']->render('document.html', array(
    'content' => $GLOBALS['data']['documents'][pathinfo($document, PATHINFO_FILENAME)],
    'data' => $GLOBALS['data']['documents'],
    'image' => $image,
    'options' => $GLOBALS['options'],
    'contactForm' => renderContactForm('List pre Pána Kráľa', '')
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

function renderContactForm($subject, $text)
{
    return $GLOBALS['twig']->render('contactform.html', array(
  'data' => $GLOBALS['data'],
  'options' => $GLOBALS['options'],
  'subject' => $subject,
  'text' => $text
));
}

function renderFormCheckboxes()
{
    return $GLOBALS['twig']->render('form-checkboxes.html', array(
    'data' => $GLOBALS['data'],
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
  'cover' => renderCover('project', $projectId, ""),
  'content' => renderProject($projectId),
  'footer' => renderFooter(),
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
      'content' => renderDocument($document),
      'footer' => renderFooter(),
      'scripts' => renderScripts(),
      'masterClass' => 'document',
      'options' => $GLOBALS['options']
  ));
}

function saveIndex()
{
    /* Save Index.html */
    $content = renderHomePage();
    file_put_contents($GLOBALS['options']['basepath'].'index.html', $content);
}

function saveProject($key)
{
    /* Save Projects' html files */
    $dir = $GLOBALS['options']['basepath'].'./'.$key.'/';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($dir.'/index.html', renderProjectPage($key));
    if (array_key_exists('gallery', $GLOBALS['data']['projects'][$key])) {
        foreach ($GLOBALS['data']['projects'][$key]['gallery'] as $key2 => $gallery) {
            $subdir = $GLOBALS['options']['basepath'].'./'.$key.'/gallery/';
            if (!file_exists($subdir)) {
                mkdir($subdir, 0777, true);
            }
            file_put_contents($subdir.'/'.$key2.'.html', renderGalleryPage($key, $key2));
        }
    }
}

function saveDocument($doc)
{
    /* Save Documents' html files */
    file_put_contents($GLOBALS['options']['basepath'].pathinfo($doc, PATHINFO_FILENAME).'.html', renderDocumentPage($doc));
}

function saveFiles()
{
    saveIndex();

    foreach ($GLOBALS['data']['projects'] as $key => $project) {
        saveProject($key);
    }

    foreach ($GLOBALS['data']['documents'] as $key => $doc) {
        if (pathinfo($doc, PATHINFO_EXTENSION) == "md") {
            saveDocument($doc);
        }
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
