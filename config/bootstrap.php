<?php

define( 'DOCROOT', dirname( dirname(__FILE__)));

spl_autoload_register( function( $class_name) {
  $class = DOCROOT . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
  if( ! file_exists( $class)) {
    return false;
  }
	require_once( $class );
  return true;
});

require_once DOCROOT.'/vendor/autoload.php';
