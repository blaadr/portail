<?php 
 namespace Workflow\Model;

 class SelligentMail
 {
     public $id;
     public $name;
     public $mailtreeid;
	 public $template;
	 public $templateid;
	 public $type;
	 public $created_dt;
	 public $modified_dt;
	 public $listid;
	 public $ownerid;
	 public $campagne_id;

     public function exchangeArray($data)
     {
         $this->id          = (!empty($data['ID']))          ? $data['ID']          : null;
         $this->name        = (!empty($data['NAME']))        ? $data['NAME']        : null;
         $this->mailtreeid  = (!empty($data['MAILTREEID']))  ? $data['MAILTREEID']  : null;
		 $this->template    = (!empty($data['TEMPLATE']))    ? $data['TEMPLATE']    : null;
		 $this->templateid  = (!empty($data['TEMPLATEID']))  ? $data['TEMPLATEID']  : null;
		 $this->type        = (!empty($data['TYPE']))        ? $data['TYPE']        : null;
		 $this->created_dt  = (!empty($data['CREATED_DT']))  ? $data['CREATED_DT']  : null;
		 $this->modified_dt = (!empty($data['MODIFIED_DT'])) ? $data['MODIFIED_DT'] : null;
		 $this->listid      = (!empty($data['LISTID']))      ? $data['LISTID']      : null;
		 $this->ownerid     = (!empty($data['OWNERID']))     ? $data['OWNERID']     : null;
		 $this->campagne_id = (!empty($data['campagne_id'])) ? $data['campagne_id'] : null;
     }
 }