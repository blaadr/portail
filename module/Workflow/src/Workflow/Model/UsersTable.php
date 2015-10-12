<?php
 namespace Workflow\Model;

 use Zend\Db\TableGateway\TableGateway;

 class UsersTable
 {
     /*
	  * le nom de la table correspondante en BDD est selligent_mails
	  */
	 protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select();
         return $resultSet;
     }

     public function getRoleUsers($role)
     {
         $resultSet = $this->tableGateway->select(array('role_id'=>$role));
         return $resultSet;
     }

     public function getChildUsers($user_id)
     {
         $resultSet = $this->tableGateway->select(array('parent_id'=>$user_id));
         return $resultSet;
     }

 }