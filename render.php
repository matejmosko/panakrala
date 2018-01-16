<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('functions.php');
require_once('lib/Twig/Autoloader.php');
 Twig_Autoloader::register();

$data = getData();

    $loader = new Twig_Loader_Filesystem('templates/');
    $twig = new Twig_Environment($loader);
renderEvents($data, 'turban', $twig);

function renderEvents($data, $project, $twig)
{
    echo $twig->render('eventlist.html', array(
      'project' => $data['projects'][$project],
      'path' => $project,
      'today' => time()
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
