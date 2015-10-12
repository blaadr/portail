<?php
 namespace Workflow\Model;

 use Zend\Db\TableGateway\TableGateway;

 class SelligentMailTable
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

     public function getSelligentMail($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function getUnattributedSelligentTemplates()
     {

         $resultSet = $this->tableGateway->select(array('TEMPLATE' => 1, 'campagne_id' => null, ));
		 return $resultSet;
     }

     public function saveSelligentMail(SelligentMail $mail)
     {
         $data = array(
             'name'        => $mail->name,
             'mailtreeid'  => $mail->mailtreeid,
             'template'    => $mail->template,
             'templateid'  => $mail->templateid,
             'type'        => $mail->type,
             'created_dt'  => $mail->created_dt,
             'modified_dt' => $mail->modified_dt,
             'listid'      => $mail->listid,
             'ownerid'     => $mail->ownerid,
             'campagne_id' => $mail->campagne_id
         );

         $id = (int) $mail->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getSelligentMail($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Album id does not exist');
             }
         }
     }

     public function deleteSelligentMail($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }