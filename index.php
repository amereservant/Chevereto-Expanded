<?php
/*** Site Constants ***/
$site_path = realpath(dirname(__FILE__));

define('CHEV_SEP', DIRECTORY_SEPARATOR);
define('CHEV_PATH', $site_path);
define('SYS_PATH', CHEV_PATH  . CHEV_SEP .'system');
define('PLUGIN_PATH', CHEV_PATH . CHEV_SEP .'plugins');

unset($site_path);

/*** Include System Files ***/
if( file_exists( CHEV_PATH . CHEV_SEP .'config.php' ) ) {
require_once CHEV_PATH . CHEV_SEP .'config.php';
}
require_once SYS_PATH . CHEV_SEP .'core.php';
require_once SYS_PATH . CHEV_SEP .'database.class.php';
require_once SYS_PATH . CHEV_SEP .'error.class.php';
require_once SYS_PATH . CHEV_SEP .'registry.class.php';
require_once SYS_PATH . CHEV_SEP .'plugins.class.php';
// Load URL parsing class
require_once SYS_PATH . CHEV_SEP .'router.class.php';

/*** Initialize/Load Everything ***/
require_once SYS_PATH . CHEV_SEP .'initialize.php';
