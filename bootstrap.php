<?php

namespace MDT;

class Bootstrap
{
/**
 * Checks if the system requirements are met
 * 
 *
 * @return bool True if system requirements are met, false if not
 */
	const NAMESPACE = "MDT";
	const SHORTCODE = "mntdbtable";
	function requirements() 
	{
		define( 'NAME','maintain-database-table' );
		define( 'REQUIRED_PHP_VERSION', '7.0' );
		define( 'REQUIRED_WP_VERSION',  '3.1' );
		global $wp_version;

		if ( version_compare( PHP_VERSION, REQUIRED_PHP_VERSION, '<' ) ) {
			return false;
		}

		if ( version_compare( $wp_version, REQUIRED_WP_VERSION, '<' ) ) {
			return false;
		}
		return true;
	}

/**
 * Prints an error that the system requirements weren't met.
 */
	function requirements_error() 
	{
		global $wp_version;
		echo notices::requirements_error();
		echo $html;
	}
	function trap()
	{
		$self = new self();
		echo notices::trap("maintain-database-table in trap" . $self::NAMESPACE );
	}
	#
	# autoloader for the classes defined in map classes
	#
	function autoloader()
	{
		spl_autoload_register(function ($class_name)
		{
			$self = new self();
			#echo $class_name;
			$parts = explode( '\\', $class_name );
			if($parts[0] == $self::NAMESPACE)
			{
				$classfile=$parts[1];
				require_once( dirname( __FILE__ ) . '/classes/' . $classfile . '.php' );
			}
		});
	}
	function init()
	{
		$this->autoloader();	#start autoloader for loading classes automatically
		$mntdbtable = new mntdbtable();
		$options = new options();
		if ( $this->requirements() ) 
		{
			# set shortcode, so plugin can be started in an article like: [maintaindbtable .... ]
			add_shortcode( 'mntdbtable', array($mntdbtable ,'init') );
			$options->init();		#make parameters form
		}  
		else 
		{
			add_action( 'admin_notices', array($this,'requirements_error') ); # error message 
		}
	}
}
