<?php
include_once(__DIR__.'/../vendor/autoload.php');

define('SRC', dirname(__DIR__).'/src');

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use JD\Application\Controller\OverviewController;

$app = new Application();
$app['debug'] = true;
$app->register( new TwigServiceProvider(), array('twig.path' => SRC.'/JD/Application/Resources/Views') );
$app->register( new ServiceControllerServiceProvider() );
$app->register( new FormServiceProvider() );
$app->register( new TranslationServiceProvider(), array( 'translator.messages' => array(), ) );
$app->register( new ValidatorServiceProvider());

//create shared service jd.controller.overview
$app['jd.controller.overview'] = $app->share(
	function() use($app) {
		return new OverviewController( $app );
	}
);

$app->match('/', 'jd.controller.overview:indexAction');

$app->run();
