<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php
 *
 * This is an application wide file to load any function that is not used within a class
 * define. You can also use this to include or require any files in your application.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * App::build(array(
 *     'plugins' => array('/full/path/to/plugins/', '/next/full/path/to/plugins/'),
 *     'models' =>  array('/full/path/to/models/', '/next/full/path/to/models/'),
 *     'views' => array('/full/path/to/views/', '/next/full/path/to/views/'),
 *     'controllers' => array('/full/path/to/controllers/', '/next/full/path/to/controllers/'),
 *     'datasources' => array('/full/path/to/datasources/', '/next/full/path/to/datasources/'),
 *     'behaviors' => array('/full/path/to/behaviors/', '/next/full/path/to/behaviors/'),
 *     'components' => array('/full/path/to/components/', '/next/full/path/to/components/'),
 *     'helpers' => array('/full/path/to/helpers/', '/next/full/path/to/helpers/'),
 *     'vendors' => array('/full/path/to/vendors/', '/next/full/path/to/vendors/'),
 *     'shells' => array('/full/path/to/shells/', '/next/full/path/to/shells/'),
 *     'locales' => array('/full/path/to/locale/', '/next/full/path/to/locale/')
 * ));
 *
 */

/**
 * As of 1.3, additional rules for the inflector are added below
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

# Any API keys or similar data
# e.g. define( 'APIKEY_IPINFODB', 'f3f849970d5353c1de35ec39d8c2e6b2c355360193f84b0bb693b78d9cbc4b7c' ); # http://ipinfodb.com

# Environment info
Configure::write( 'env.name', 'Production' );
Configure::write( 'env.code', 'PRD' );
Configure::write( 'env.domain', 'www.savebigbread.com' );
Configure::write( 'env.full_base_url', 'http://www.savebigbread.com' );

# Feature toggle switches
# Configure::write( 'feature.contractor_registration.enabled', true );

# Date/time formatting constants 
define( 'DATE_FORMAT_SHORT', 'm/d/Y' );
define( 'DATE_FORMAT_LONG_WITH_DAY', 'l, F j, Y' );
define( 'DATE_FORMAT_LONG', 'F j, Y' );
define( 'DATE_FORMAT_MYSQL', 'Y-m-d' );

define( 'TIME_FORMAT', 'g:i a' );
define( 'TIME_FORMAT_MYSQL', 'H:i:s' );

define( 'DATETIME_FORMAT_SHORT', DATE_FORMAT_SHORT . ' ' . TIME_FORMAT );
define( 'DATETIME_FORMAT_LONG', DATE_FORMAT_LONG . ' ' . TIME_FORMAT );
define( 'DATETIME_FORMAT_MYSQL', DATE_FORMAT_MYSQL . ' ' . TIME_FORMAT_MYSQL );

# Cache configuration
Cache::config(
  'brief',
  array(
    'engine' => 'File',
    'duration'=> '+15 minutes',
    'probability'=> 100,
    'path' => CACHE . 'brief' . DS,
  )
);
Cache::config(
  'hour',
  array(
    'engine' => 'File',
    'duration'=> '+1 hour',
    'probability'=> 100,
    'path' => CACHE . 'hour' . DS,
  )
);
Cache::config(
  'day',
  array(
    'engine' => 'File',
    'duration'=> '+1 day',
    'probability'=> 100,
    'path' => CACHE . 'day' . DS,
  )
);
Cache::config(
  'week',
  array(
    'engine' => 'File',
    'duration'=> '+1 week',
    'probability'=> 100,
    'path' => CACHE . 'week' . DS,
  )
);

if( file_exists( dirname( __FILE__ ) . DS . 'bootstrap.local.php' ) ) {
  include_once( 'bootstrap.local.php' );
}
