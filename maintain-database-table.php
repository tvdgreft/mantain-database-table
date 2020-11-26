<?php

namespace MDT;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
Plugin Name: maintain-database-table
Plugin URI:  https://github.com/pranamas/wp/maintan-database-table
Description: Display records of a database table and modify records
Version:     1.0
Author:      Theo van der Greft
Author URI:  http://www.pranamas.nl
*/
#require_once dirname( __FILE__ ) . '/options.php';
require_once dirname( __FILE__ ) . '/bootstrap.php';
$bootstrap = new Bootstrap();
$bootstrap->init();
?>
