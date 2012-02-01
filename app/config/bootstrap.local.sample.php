<?php 

# Override config/core.php
Configure::write( 'debug', 1 );
Configure::write( 'Cache.disable', true );

# Override config/bootstrap.php and/or new config values
Configure::write( 'Env.name', 'Localhost' );
Configure::write( 'Env.code', 'LCL' );
Configure::write( 'Env.domain', 'thedomain.dev' );

Configure::write( 'email.redirect_to', 'you@yourdomain.com' );

# Ignore PHPDump if running from the console
/* 
if( !defined( 'STDIN' ) && function_exists( 'ini_set' ) ) {
  ini_set( 'include_path', DS . 'var' . DS . 'www' . DS . '__phplib' . DS . PATH_SEPARATOR . ini_get('include_path') );
  include_once( 'org/robwilkerson/io/phpdump.php' );
}
*/