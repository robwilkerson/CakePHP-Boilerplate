<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::import('View', 'Helper', false);

/**
 * This is a placeholder class.
 * Create the same file in app/app_helper.php
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.cake
 */
class AppHelper extends Helper {
  /** 
   * Overrides the default url method in order to clear any/all prefixes
   * that aren't explicitly requested. This will prevent a subsequent request
   * (e.g. redirect or link on a prefixed page) from inheriting the current
   * prefix.
   * 
   * @access  public
   * @see     Helper::url()
   */
  function url( $url = null, $full = false ) { 
    if( isset( $this->params['prefix'] ) ) { 
      $prefix = $this->params['prefix']; 

      if( $this->params[$prefix] && ( !isset( $url[$prefix] ) || empty( $url[ $prefix ] ) ) ) { 
        $url[$prefix] = false; 
      } 
    } 

    return parent::url( $url, $full ); 
  } 
}
