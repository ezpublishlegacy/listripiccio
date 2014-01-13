<?php
/**
 * @copyright Copyright (C) 2013 land in sicht AG All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

$FunctionList = array();

$FunctionList['register'] = array(
      'name' => 'register',
      'call_method' => array( 
      'include_file' => 'extension/tripiccio/modules/tripiccio/tripiccio.class.php',
      'class' => 'tripiccio',
      'method' => 'register' ),
      'parameter_type' => 'standard',
      'parameters' => array( array( 'name' => 'collection',
                                    'required' => true,
                                    'default' => false )));

$FunctionList['unsubscribe'] = array(
      'name' => 'unsubscribe',
      'call_method' => array( 
      'include_file' => 'extension/tripiccio/modules/tripiccio/tripiccio.class.php',
      'class' => 'tripiccio',
      'method' => 'unsubscribe' ),
      'parameter_type' => 'standard',
      'parameters' => array( array( 'name' => 'data',
                                    'required' => true,
                                    'default' => false )));
?>
