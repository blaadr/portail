<?php
 namespace Workflow;

 use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
 use Zend\ModuleManager\Feature\ConfigProviderInterface;
 use Zend\Db\ResultSet\ResultSet;
 use Zend\Db\TableGateway\TableGateway; 
 use Workflow\Model\SelligentMail;
 use Workflow\Model\SelligentMailTable;
 use Workflow\Model\Campagne;
 use Workflow\Model\CampagneTable;
 use Workflow\Model\Users;
 use Workflow\Model\UsersTable;
 use Workflow\Model\StepsTable;

 class Module implements AutoloaderProviderInterface, ConfigProviderInterface
 {
     public function getAutoloaderConfig()
     {
         return array(
             'Zend\Loader\ClassMapAutoloader' => array(
                 __DIR__ . '/autoload_classmap.php',
             ),
             'Zend\Loader\StandardAutoloader' => array(
                 'namespaces' => array(
                     __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                 ),
             ),
         );
     }

     public function getConfig()
     {
         return include __DIR__ . '/config/module.config.php';
     }
	 
     public function getServiceConfig()
     {
         return array(
             'factories' => array(
				//SelligentMail table
                 'Workflow\Model\SelligentMailTable' =>  function($sm) {
                     $tableGateway = $sm->get('SelligentMailTableGateway');
                     $table = new SelligentMailTable($tableGateway);
                     return $table;
                 },
                 'SelligentMailTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new SelligentMail());
                     return new TableGateway('selligent_mails', $dbAdapter, null, $resultSetPrototype);
                 },
				//Campagne table
                 'Workflow\Model\CampagneTable' => function($sm) {
                     $tableGateway = $sm->get('CampagneTableGateway');
                     $table = new CampagneTable($tableGateway);
                     return $table;
                 },
                 'CampagneTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Campagne());
                     return new TableGateway('campagnes', $dbAdapter, null, $resultSetPrototype);
                 },
				 //Steps table
                 'Workflow\Model\StepsTable' => function($sm) {
                     $tableGateway = $sm->get('StepsTableGateway');
                     $table = new StepsTable($tableGateway);
                     return $table;
                 },
                 'StepsTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     //$resultSetPrototype->setArrayObjectPrototype(new Campagne());
                     return new TableGateway('steps', $dbAdapter, null, $resultSetPrototype);
                 },
				//Users table
                 'Workflow\Model\UsersTable' => function($sm) {
                     $tableGateway = $sm->get('UsersTableGateway');
                     $table = new UsersTable($tableGateway);
                     return $table;
                 },
                 'UsersTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Users());
                     return new TableGateway('users_role', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }

 }