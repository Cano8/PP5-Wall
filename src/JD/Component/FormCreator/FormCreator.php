<?php

namespace JD\Component\FormCreator;

use Symfony\Component\Validator\Constraints as Assert;

class FormCreator
{
	protected $app;
	protected $request;
	
	public function __construct($app, $request) {
		$this->app = $app;
		$this->request = $request;
	}
	
	public function createForm($entries, $json) {
		$app = $this->app;
		$request = $this->request;
		
		$form = $app['form.factory']->createBuilder('form')
			->add('attachment', 'file', array(
				'constraints' => array( new Assert\Image( array( 
						'minWidth' => 120,
						'maxWidth' => 1080,
						'minHeight' => 120,
						'maxHeight' => 1080,
					))
				)	
			))
			->add('picture')
			->add('about', 'textarea', array(
				'constraints' => array(new Assert\Length(array('min' => 5)))
			))
			->add('number')
			->add('location')
			->getForm();
			
		$form->handleRequest($request);

		if ($form->isValid()) {
			$data = $form->getData();
			$number = $data['number'];
			
			if ($data['attachment'] != '' ) {
				$data['attachment']->move('./img/usr', $number );
				$entries[$number]->setPicture('./img/usr/'.$number, $json);
			} else {
				if ($data['picture'] != '' )
					$entries[$number]->setPicture($data['picture'], $json);
			}

			if ($data['about'] != '' )
				$entries[$number]->setAbout($data['about'], $json);
			if ($data['location'] != '' )
				$entries[$number]->setLocation($data['location'], $json);
			return 'formIsValid';
		}
		return $form;
	}
}
