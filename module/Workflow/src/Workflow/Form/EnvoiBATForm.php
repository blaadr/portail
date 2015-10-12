<?php
 namespace Workflow\Form;

 use Zend\Form\Form;

 class EnvoiBATForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('envoiBAT');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',

         ));
         $this->add(array(
             'name' => 'envoiBAT',
             'type' => 'Checkbox',
			 'options' =>array(
				'unchecked_value' => 0,
				'checked_value' => 1,
			 ),
         ));
         $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Go',
                 'id' => 'submitbutton',
             ),
         ));
		 
     }

 }