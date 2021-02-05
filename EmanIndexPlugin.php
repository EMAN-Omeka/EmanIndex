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
  protected $_filters = array(
  	'admin_navigation_main',
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
   		$router->addRoute(
  				'ei_eman_index_fields_list',
  				new Zend_Controller_Router_Route(
  						'emanindex/fieldslist',
  						array(
  								'module' => 'eman-index',
  								'controller'   => 'page',
  								'action'       => 'fields-list',
  						)
  				)
  		);
   		$router->addRoute(
  				'ei_eman_index_preferences',
  				new Zend_Controller_Router_Route(
  						'emanindex/preferences',
  						array(
  								'module' => 'eman-index',
  								'controller'   => 'page',
  								'action'       => 'preferences',
  						)
  				)
  		);
   		$router->addRoute(
  				'ei_eman_index_update_value',
  				new Zend_Controller_Router_Route(
  						'emanindexupdate',
  						array(
  								'module' => 'eman-index',
  								'controller'   => 'ajax',
  								'action'       => 'update',
  						)
  				)
  		);
    }


  /**
   * Add the pages to the public main navigation options.
   *
   * @param array Navigation array.
   * @return array Filtered navigation array.
   */
  public function filterAdminNavigationMain($nav)
  {
    $nav[] = array(
                    'label' => __('Eman Index'),
                    'uri' => url('emanindex/fieldslist'),
                  );
    return $nav;
  }
}