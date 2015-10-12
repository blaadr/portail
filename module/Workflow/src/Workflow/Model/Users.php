<?php 
 namespace Workflow\Model;

 class Users
 {
     public $user_id;
     public $username;
     public $email;
	 public $role_id;

     public function exchangeArray($data)
     {
         $this->user_id  = (!empty($data['user_id']))  ? $data['user_id']  : null;
         $this->username = (!empty($data['username'])) ? $data['username'] : null;
         $this->email    = (!empty($data['email']))    ? $data['email']    : null;
         $this->role_id  = (!empty($data['role_id']))  ? $data['role_id']  : null;
     }
 }