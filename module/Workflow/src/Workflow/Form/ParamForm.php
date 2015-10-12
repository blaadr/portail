<?php
 namespace Workflow\Form;

 use Zend\Form\Form;

 class ParamForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('campagne');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',

         ));
		 
         $this->add(array(
             'name' => 'selligent_mailid',
             'type' => 'Hidden',

         ));
         $this->add(array(
             'name' => 'name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Nom',
             ),
         ));
         $this->add(array(
             'name' => 'type_campagne',
             'type' => 'Select',
             'options' => array(
                 'label' => 'Type de campagne',
				 'value_options' => array(
					'email'=>'Email', 
					'print'=> 'Print',
				 ),
             ),
         ));		 
         $this->add(array(
			 'type' => 'Zend\Form\Element\MultiCheckbox',
			 'name' => 'assigned_users',
			 'options' => array(
				'label' => 'qui ?',
			) 
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