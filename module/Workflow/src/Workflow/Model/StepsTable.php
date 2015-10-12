<?php
 namespace Workflow\Model;

 use Zend\Db\TableGateway\TableGateway;
 
 class StepsTable
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

     public function getStep($type_campagne, $step)
     {
         $rowset = $this->tableGateway->select(array('type_campagne' => $type_campagne, 'num' => $step));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row ");
         }
         return $row;
     }
 
 }