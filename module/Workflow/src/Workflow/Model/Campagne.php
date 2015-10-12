<?php
namespace Workflow\Model;

 // Add these import statements
 use Zend\InputFilter\InputFilter;
 use Zend\InputFilter\InputFilterAwareInterface;
 use Zend\InputFilter\InputFilterInterface;

 class Campagne implements InputFilterAwareInterface
 {
     public $id;
     public $name;
     public $create_user;
     public $create_date;
     public $selligent_mailid;
     public $level = 0;
     public $parent;
     public $assigned_users;
     public $type_campagne;
     public $step = 1;
	 
     public $children;
	 
     protected $inputFilter;

     public function exchangeArray($data)
     {
         $this->id               = (!empty($data['id']))               ? $data['id']               : null;
         $this->name             = (!empty($data['name']))             ? $data['name']             : $this->name;
         $this->create_user      = (!empty($data['create_user']))      ? $data['create_user']      : $this->create_user;
         $this->create_date      = (!empty($data['create_date']))      ? $data['create_date']      : $this->create_date;
         $this->selligent_mailid = (!empty($data['selligent_mailid'])) ? $data['selligent_mailid'] : $this->selligent_mailid;
         $this->level            = (!empty($data['level']))            ? $data['level']            : $this->level;
         $this->parent           = (!empty($data['parent']))           ? $data['parent']           : $this->parent;
         $this->assigned_users   = (!empty($data['assigned_users']))   ? $data['assigned_users']   : $this->assigned_users;
         $this->type_campagne    = (!empty($data['type_campagne']))    ? $data['type_campagne']    : $this->type_campagne;
         $this->step             = (!empty($data['step']))             ? $data['step']             : $this->step;
     }

     public function exchangeObject($data)
     {
         $this->id               = (!empty($data->id))               ? $data->id               : null;
         $this->name             = (!empty($data->name))             ? $data->name             : $this->name;
         $this->create_user      = (!empty($data->create_user))      ? $data->create_user      : $this->create_user;
         $this->create_date      = (!empty($data->create_date))      ? $data->create_date      : $this->create_date;
         $this->selligent_mailid = (!empty($data->selligent_mailid)) ? $data->selligent_mailid : $this->selligent_mailid;
         $this->level            = (!empty($data->level))            ? $data->level            : $this->level;
         $this->parent           = (!empty($data->parent))           ? $data->parent           : $this->parent;
         $this->assigned_users   = (!empty($data->assigned_users))   ? $data->assigned_users   : $this->assigned_users;
         $this->type_campagne    = (!empty($data->type_campagne))    ? $data->type_campagne    : $this->type_campagne;
         $this->step             = (!empty($data->step))             ? $data->step             : $this->step;
     }

	 public function switchAssigned_users()
	 {
		 if(is_array($this->assigned_users))
		 {
		 	$this->assigned_users = implode('|', $this->assigned_users);
		 }elseif(is_object($this->assigned_users)){
			$this->assigned_users = explode('|', $this->assigned_users);
		 }
		
	 }
	 
	 public function stepUp()
	 {
		$this->step++;

	 }
	 
     public function setInputFilter(InputFilterInterface $inputFilter)
     {
         throw new \Exception("Not used");
     }

     public function getInputFilter()
     {
         if (!$this->inputFilter) {
             $inputFilter = new InputFilter();

             $inputFilter->add(array(
                 'name'     => 'id',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'Int'),
                 ),
             ));

             $inputFilter->add(array(
                 'name'     => 'selligent_mailid',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'Int'),
                 ),
				 'validators' => array(
                     array(
                         'name'    => 'GreaterThan',
                         'options' => array(
                             'min'      => 1,
                         ),
					 ),
				 ),
             ));

             $inputFilter->add(array(
                 'name'     => 'name',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                 'validators' => array(
                     array(
                         'name'    => 'StringLength',
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 1,
                             'max'      => 100,
                         ),
                     ),
                 ),
             ));


             $inputFilter->add(array(
                 'name'     => 'type_campagne',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                 'validators' => array(
                     array(
                         'name'    => 'StringLength',
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 1,
                             'max'      => 6,
                         ),
                     ),
                 ),
             ));

             $this->inputFilter = $inputFilter;
         }

         return $this->inputFilter;
     }
	 
     public function getArrayCopy()
     {
         return get_object_vars($this);
     }


 }
