<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/


if ( !defined('AREA') ) { die('Access denied'); }

/*
 * PHP options
 */

// Disable notices displaying
error_reporting(E_ALL ^ E_NOTICE);
if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
	error_reporting(error_reporting() & ~E_DEPRECATED);
}

// Set maximum memory limit
@ini_set('memory_limit', '48M');

// Set maximum time limit for script execution
@set_time_limit(3600);

/*
 * Database connection options
 */
$config['db_host'] = 'localhost';
$config['db_name'] = 'amadamt_development';
$config['db_user'] = 'root';
$config['db_password'] = '';
$config['db_type'] = 'mysqli';

/*
 * Script location options
 *
 *	Example:
 *	Your url is http://www.yourcompany.com/store/cart
 *	$config['http_host'] = 'www.yourcompany.com';
 *	$config['http_path'] = '/store/cart';
 * 
 *	Your secure url is https://secure.yourcompany.com/secure_dir/cart
 *	$config['https_host'] = 'secure.yourcompany.com';
 *	$config['https_path'] = '/secure_dir/cart';
 *
 */

// Host and directory where software is installed on no-secure server
$config['http_host'] = 'localhost.amadamt.com';
$config['http_path'] = '';

// Host and directory where software is installed on secure server
$config['https_host'] = 'localhost.amadamt.com';
$config['https_path'] = '';

/*
 * Misc options
 */
// Names of index files for administrative and customer areas
$config['admin_index'] = 'amadaadmin.php';
$config['customer_index'] = 'index.php';
$config['vendor_index'] = 'vendor.php';

// DEMO mode
$config['demo_mode'] = false;

// Tweaks
$config['tweaks'] = array (
	'js_compression' => false, // enables compession to reduce size of javascript files
	'check_templates' => true, // disables templates checking to improve template engine speed
	'inline_compilation' => true, // compiles nested templates in one file
	'anti_csrf' => false, // protect forms from CSRF attacks
	'disable_block_cache' => false, // used to disable block cache
	'join_css' => false, // is used to unite css files into one file
	'allow_php_in_templates' => false, // Allow to use {php} tags in templates
);

// Cache backend
// Available backends: file, sqlite, mysql, shmem
// To use sqlite cache the "sqlite3" PHP module should be installed
// To use shmem cache the "shmop" PHP module should be installed
$config['cache_backend'] = 'file';

// Key for sensitive data encryption
$config['crypt_key'] = 'YOURVERYSECRETKEY';

// Database tables prefix
define('TABLE_PREFIX', 'cscart_');

// Default permissions for newly created files and directories
define('DEFAULT_FILE_PERMISSIONS', 0666);
define('DEFAULT_DIR_PERMISSIONS', 0777);

// Maximum number of files, stored in directory. You may change this parameter straight after a store was installed. And you must not change it when the store has been populated with products already.
define('MAX_FILES_IN_DIR', 1000);

// Developer configuration file
if (file_exists(DIR_ROOT . '/local_conf.php')) {
	include(DIR_ROOT . '/local_conf.php');
}

define('AUTH_CODE', '4Y2YZNGM');

?>