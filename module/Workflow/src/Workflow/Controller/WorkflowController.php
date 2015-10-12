<?php
 namespace Workflow\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use BjyAuthorize\Provider\Identity\ZfcUserZendDb;
 use Workflow\Model\Campagne;
 use Workflow\Form\CampagneForm;
 use Workflow\Form\EnvoiBATForm;
 
 class WorkflowController extends AbstractActionController
 {
     protected $selligentMailTable;
     protected $campagneTable;
     protected $usersTable;
     protected $stepsTable;

     public function indexAction()
     {
		switch ($this->getCurrentUserRole())
		{
			case 'distributeur' :
				return new ViewModel($this->indexAction_distrib());
				break;
			case 'cep' : 
				return new ViewModel($this->indexAction_cep());
				break;
			case 'admin' : 
				return new ViewModel($this->indexAction_admin());
				break;
			case 'super-admin' : 
				return new ViewModel($this->indexAction_admin());
				break;
		}
     }

     public function newAction()
     {
		 $id = (int) $this->params()->fromRoute('id', 0);
         $form = new CampagneForm();
		 $form->get('selligent_mailid')->setValue($id);
         $form->get('submit')->setValue('Ajouter');
		 $form->get('assigned_users')->setOptions(array('value_options' => $this->getUsersWithRoleCEP()));
         $request = $this->getRequest();
         if ($request->isPost()) {
             $campagne = new Campagne();
             $campagne->selligent_mailid = $id;
			 $campagne->create_user = $this->zfcUserAuthentication()->getIdentity()->getUsername();
             $form->setInputFilter($campagne->getInputFilter());
             $form->setData($request->getPost());
             if ($form->isValid()) {
                 $campagne->exchangeArray($form->getData());
                 $campagne_id = $this->getCampagneTable()->insertCampagne($campagne);
				 $selligent_mail = $this->getSelligentMailTable()->getSelligentMail($campagne->selligent_mailid);
				 $selligent_mail->campagne_id = $campagne_id;
				 $this->getSelligentMailTable()->saveSelligentMail($selligent_mail);
                 return $this->redirect()->toRoute('workflow');
             }
         }
         return array('form' => $form, 'id'=> $id);
     }

     public function campagneAction()
     {
		$id = (int) $this->params()->fromRoute('id', 0);
		if($id == 0){
			return $this->redirect()->toRoute('workflow');
		}
		switch ($this->getCurrentUserRole())
		{
			case 'distributeur' :
				return new ViewModel($this->campagneAction_distrib($id));
				break;
			case 'cep' : 
				return new ViewModel($this->campagneAction_cep($id));
				break;
			case 'admin' : 
				return new ViewModel($this->campagneAction_admin($id));
				break;
			case 'super-admin' : 
				return new ViewModel($this->campagneAction_admin($id));
				break;
		}
		 


     }

	 
	/************************************************************************
	 *
	 * DECLINAISON DES ACTIONS PAR ROLE
	 *
	 ************************************************************************/
	 
	/***************************** ACTION INDEX *****************************/
	/************************************************************************/ 
	 public function indexAction_admin()
	 {
		return array('unattributedTemplates' => $this->getSelligentMailTable()->getUnattributedSelligentTemplates(),
					 'campaigns' => $this->getCampagneTable()->getCampagnesByLevel(0),
					 'messages' => "Administrateur<br/>",
					);
	 }

	 public function indexAction_cep()
	 {
		$campaigns = $this->getCampagneTable()->getCampagnesByUser($this->zfcUserAuthentication()->getIdentity()->getUsername());
		$c = array();
		foreach ($campaigns as $campaign)
		{
			$campaign->children == $this->getCampagneTable()->getCampagnesByParent($campaign->id);
			array_push($c, $campaign);
		}
		return array('campaigns' => $c,
					 'messages' => "CEP<br/>",
					);
	 }

	 public function indexAction_distrib()
	 {
		$campaigns = $this->getCampagneTable()->getCampagnesByUser($this->zfcUserAuthentication()->getIdentity()->getUsername());
		$c = array();
		foreach ($campaigns as $campaign)
		{
			$campaign->children == $this->getCampagneTable()->getCampagnesByParent($campaign->id);
			array_push($c, $campaign);
		}
		return array('campaigns' => $c,
					 'messages' => "Distributeur<br/>",
					);
	 }


	
	/*************************** ACTION CAMPAGNE ****************************/
	/************************************************************************/ 
	 public function campagneAction_admin($id)
	 {
		$campagnes = $this->getCampagneTable()->getCampagnesByParent($id);
		$c = array();
		foreach($campagnes as $campagne)
		{
			$campagne->children = $this->getCampagneTable()->getCampagnesByParent($campagne->id);
			array_push($c, $campagne);
		}
		return array(
			'role' => 'admin',
			'mother_campaign' => $this->getCampagneTable()->getCampagne($id),
			'campaigns' => $c,
		);
	 }
	 
	 public function campagneAction_cep($id)
	 {
		$campaign = $this->getCampagneTable()->getCampagne($id);
		$step = $this->getStepsTable()->getStep($campaign->type_campagne, $campaign->step);
		$campaign->children = $this->getCampagneTable()->getCampagnesByParent($campaign->id);
		$form = $this->getStepForm($campaign->type_campagne, $campaign->step);
		if(!empty($form)){
			switch ($form->getName()){
				case 'campagnes' :
					$form->bind($campaign);
					$form->get('submit')->setValue('Ajouter');
					$form->get('assigned_users')->setOptions(array('value_options' => $this->getChildUsers()));
					break;
				case 'envoiBAT' :
					break;
			}
		}
		$request = $this->getRequest();
		if ($request->isPost()) {
			$campagne = new Campagne();
			$campagne->selligent_mailid = $id;
			$form->setInputFilter($campagne->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$campagne->exchangeObject($form->getData());
				$campagne->create_user = $this->zfcUserAuthentication()->getIdentity()->getUsername();
				$this->getCampagneTable()->insertChildCampagne($campagne);
				return $this->redirect()->toRoute('workflow');
			}
		}
		return array(
			'role' => 'cep',
			'campaign' => $campaign,
			'form' => $form,
		);
	 }

	 public function campagneAction_distrib($id)
	 {
		return array(
			'role' => 'distributeur',
		);
	 }

	/************************************************
	 * FONCTIONS SPECIFIQUES
	 **********************************************/
	 public function getStepForm($type_campagne, $step)
	 {
		switch ($this->getCurrentUserRole())
		{
			case 'super-admin' : 
				if($type_campagne =='email')
				{
					if($step==0)
					{
						new CampagneForm();
					}
					elseif($step==5)
					{
						new TraitementFichierForm();
					}
				}
				break;
			case 'admin' : 
				if($type_campagne =='email')
				{
					if($step==0)
					{
						new CampagneForm();
					}
				}
				break;
			case 'cep' : 
				if($type_campagne =='email')
				{
					if($step==1)
					{
						return new CampagneForm();
					}
					elseif($step==2)
					{
						return new EnvoiBATForm();
					}
					elseif($step==6)
					{
						new LancementCampagneForm();
					}
				}
				break;
			case 'distributeur' :
				if($type_campagne =='email')
				{
					if($step==3)
					{
						new ValidationBATForm();
					}
					elseif($step==4)
					{
						new ImportFichierForm();
					}
				}
				break;
		}
	 }

	 public function getCurrentUserRole()
	 {
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		$user = $sm->get('zfcuseruserservice');
		$identityProvider = new ZfcUserZendDb($dbAdapter, $user);
		$roles = $identityProvider->getIdentityRoles();
		$role = $roles[0];
		return $role;
	 }
	 
	 public function getUsersWithRoleCEP()
     {
		$rs = $this->getUsersTable()->getRoleUsers('cep');
		$arr = array();
		foreach ($rs as $row){
			$arr[$row->username] = $row->username;
		}
		return $arr;
     }

	 public function getChildUsers()
     {
		$rs = $this->getUsersTable()->getChildUsers($this->zfcUserAuthentication()->getIdentity()->getId());
		$arr = array();
		foreach ($rs as $row){
			$arr[$row->username] = $row->username;
		}
		return $arr;
     }

     public function getSelligentMailTable()
     {
         if (!$this->selligentMailTable) {
             $sm = $this->getServiceLocator();
             $this->selligentMailTable = $sm->get('Workflow\Model\SelligentMailTable');
         }
         return $this->selligentMailTable;
     }
	 
     public function getCampagneTable()
     {
         if (!$this->campagneTable) {
             $sm = $this->getServiceLocator();
             $this->campagneTable = $sm->get('Workflow\Model\CampagneTable');
         }
         return $this->campagneTable;
     }
	 
     public function getStepsTable()
     {
         if (!$this->stepsTable) {
             $sm = $this->getServiceLocator();
             $this->stepsTable = $sm->get('Workflow\Model\StepsTable');
         }
         return $this->stepsTable;
     }
	 
     public function getUsersTable()
     {
         if (!$this->usersTable) {
             $sm = $this->getServiceLocator();
             $this->usersTable = $sm->get('Workflow\Model\UsersTable');
         }
         return $this->usersTable;
     }
 }