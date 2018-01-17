<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('functions.php');
require_once('lib/Twig/Autoloader.php');
 Twig_Autoloader::register();

$data = getData();

    $loader = new Twig_Loader_Filesystem('templates/');
    $twig = new Twig_Environment($loader);
renderEvents($data, 'turban', $twig, 'all');

function filterEvents($data, $project, $scope)
{
  $today = time();
    switch ($scope) {
  case 'all':
    return $data;
    break;
  case 'next':
    foreach ($data['projects'][$project]['events'] as $key => &$event) {
      echo "wow";
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
}

function renderEvents($localData, $project, $twig, $scope)
{
    $today = time();
    $localData = filterEvents($localData, $project, $scope);

    echo $twig->render('eventlist.html', array(
      'project' => $localData['projects'][$project],
      'path' => $project,
      'today' => $today,
      'scope' => $scope /* all, next, past */
    ));

    /* echo "<pre>";
      print_r($data['projects']['turban']);
      echo "</pre>";*/
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
