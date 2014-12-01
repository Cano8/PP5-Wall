<?php

namespace JD\Application\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use JD\Component\JsonManager\JsonManager;
use JD\Component\Entry\Entry;
use JD\Component\Entry\Entries;
use JD\Component\FormCreator\FormCreator;

class OverviewController
{
	protected $app;
	
	public function __construct($app) {
		$this->app = $app;
	}
	
	public function indexAction(Request $request) {
	
		$app = $this->app;
		$twig = $this->app['twig'];
		$response;
		$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

		$jsonManager = new JsonManager('../src/JD/Application/Resources/Data/feed.json');

		//var_dump($_POST);
		//var_dump($jsonManager);
		//var_dump($request);
		
		if (key($_POST) == 'remove' ) {
			$jsonManager->removeEntryJson($_POST['remove']);
		}
		
		if (key($_POST) == 'add' ) {
			$jsonManager->addEntryJson();
		}
		
		$entriesManager = new Entries($jsonManager->findAll());
		$entries = $entriesManager->getEntries();

		if($isAjax) {
			switch ( key($_POST) ) {
				case 'requestForm': case 'form':
				
					$formCreator = new FormCreator($app, $request);
					$form = $formCreator->createForm($entries, $jsonManager);
					
					if ($form == 'formIsValid') 
					{
						$response = $twig->render(
								'entries.html.twig',
								array(
									'entries' => $entries,
									'i' => count($entries)-1,
								)
							);
					} else {
						$response = $twig->render(
								'form.html.twig',
								array(
									'form' => $form->createView(),
								)
							);
					}					
					
					break;	
				case 'add':
					$response = $twig->render(
							'entries.html.twig',
							array(
								'entries' => $entries,
								'i' => count($entries)-1,
							)
						);
					break;
				case 'remove':
					$response = $twig->render(
							'entries.html.twig',
							array(
								'entries' => $entries,
								'i' => count($entries)-1,
							)
						);
					break;
				default:
					break;
			}
		} else {
			//echo var_dump($_POST);
		
			$response = $twig->render(
				'index.html.twig',
				array(
					'entries' => $entries,
					'form' => '',
				)
			);
		}
				
		return new Response ( $response );
	}
}