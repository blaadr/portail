<?php
 return array(
     'controllers' => array(
         'invokables' => array(
             'Workflow\Controller\Workflow' => 'Workflow\Controller\WorkflowController',
         ),
     ),
	 
     'router' => array(
         'routes' => array(
             'workflow' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/workflow[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Workflow\Controller\Workflow',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),

     'view_manager' => array(
         'template_path_stack' => array(
             'workflow' => __DIR__ . '/../view',
         ),
     ),
 );