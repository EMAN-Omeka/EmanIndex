<?php 
/**
 * Item Relations
 * @copyright Copyright 2010-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Item Relations plugin.
 */
class EmanIndexPlugin extends Omeka_Plugin_AbstractPlugin
{
  protected $_hooks = array(
        'define_routes',
        );  
        
  function hookDefineRoutes($args)
    {
      		$router = $args['router'];
       		$router->addRoute(
      				'ei_eman_index_fields',
      				new Zend_Controller_Router_Route(
      						'emanindexfields/:idfield', 
      						array(
      								'module' => 'eman-index',
      								'controller'   => 'page',
      								'action'       => 'fetchfields',
      								'idfield'					=> ''
      						)
      				)
      		);
       		$router->addRoute(
      				'ei_eman_index_page',
      				new Zend_Controller_Router_Route(
      						'emanindexpage/:idfield', 
      						array(
      								'module' => 'eman-index',
      								'controller'   => 'page',
      								'action'       => 'indexcomplet',
      								'idfield'					=> ''
      						)
      				)
      		);      		
    }
  

}