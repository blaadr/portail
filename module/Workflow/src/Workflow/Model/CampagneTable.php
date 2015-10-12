<?php
 namespace Workflow\Model;

 use Zend\Db\TableGateway\TableGateway;
 use Zend\Db\Sql\Select;

 
 class CampagneTable
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

     public function getCampagne($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }
	 
     public function getCampagnesByLevel($lvl)
     {
         $lvl  = (int) $lvl;
         $resultset = $this->tableGateway->select(array('level' => $lvl));
         return $resultset;
     }

     public function getCampagnesByParent($id)
     {
         $id  = (int) $id;
         $resultset = $this->tableGateway->select(array('parent' => $id));
         return $resultset;
     }

     public function getCampagnesByUser($username)
     {
		$resultset = $this->tableGateway->select(array('assigned_users' => $username));
        return $resultset;
     }

     public function insertCampagne(Campagne $campagne)
     {
         $campagne->switchAssigned_users();
         $data = array(
             'name'             => $campagne->name,
             'create_user'      => $campagne->create_user,
             'create_date'      => date('Y-m-d H:y:s'),
             'selligent_mailid' => $campagne->selligent_mailid,
             'level'            => $campagne->level,
             'parent'           => $campagne->parent,
             'assigned_users'   => $campagne->assigned_users,
             'type_campagne'    => $campagne->type_campagne,
             'step'             => $campagne->step
         );
         $id = (int) $campagne->id;
         if ($id == 0) {
			//insert de la campagne mere
            $this->tableGateway->insert($data);
			
			//recuperation de l'id fraichement inséré
			$id = $this->tableGateway->getLastInsertValue();
			
			//insert des campagnes filles
			$campagne->switchAssigned_users();
			foreach($campagne->assigned_users as $usr)
			{
				$data = array(
					'name'             => $campagne->name,
					'create_user'      => $campagne->create_user,
					'create_date'      => date('Y-m-d H:y:s'),
					'selligent_mailid' => $campagne->selligent_mailid,
					'level'            => (int)$campagne->level + 1,
					'parent'           => $id,
					'assigned_users'   => $usr,
					'type_campagne'    => $campagne->type_campagne,
					'step'             => $campagne->step
				);
				$this->tableGateway->insert($data);
			}
			
			//update du mail selligent dans le controller
			return $id;
         }
     }

    public function insertChildCampagne(Campagne $campagne)
    {
		$campagne->stepUp();
		
		foreach($campagne->assigned_users as $usr)
		{
			$data = array(
				'name'             => $campagne->name,
				'create_user'      => $campagne->create_user,
				'create_date'      => date('Y-m-d H:y:s'),
				'selligent_mailid' => $campagne->selligent_mailid,
				'level'            => (int)$campagne->level + 1,
				'parent'           => $campagne->id,
				'assigned_users'   => $usr,
				'type_campagne'    => $campagne->type_campagne,
				'step'             => $campagne->step
			);
			$this->tableGateway->insert($data);
		}

		$campagne->assigned_users = $campagne->create_user;
		$this->saveCampagne($campagne);
    }

     public function saveCampagne(Campagne $campagne)
     {
         $campagne->switchAssigned_users();
		 
         $data = array(
             'name'             => $campagne->name,
             'selligent_mailid' => $campagne->selligent_mailid,
             'level'            => $campagne->level,
             'parent'           => $campagne->parent,
			 'assigned_users'   => $campagne->assigned_users,
             'type_campagne'    => $campagne->type_campagne,
			 'step'             => $campagne->step
         );
         $id = (int) $campagne->id;
         if ($id != 0) {
             if ($this->getCampagne($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Campagne id does not exist');
             }
         }
     }

     public function deleteCampagne($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 
 }