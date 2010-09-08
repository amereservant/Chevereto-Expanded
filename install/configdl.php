<?php
/**
 * Configuration File Download
 *
 * This script is used to create a file download of the configuration file data when
 * it isn't possible for the script to write it to disk.
 * It should be passed a string with the config.php file's contents, un-escaped.
 *
 * PHP5
 *
 * @package     Chevereto
 * @author      David Miles <david@amereservant.com>
 * @version     2.0
 * @since       2.0
 * @license     http://creativecommons.org/licenses/MIT/ MIT License
 * @TODO        Test on a server with magic quotes on and see if it's a problem.
 */
if( !isset($_POST['config_create_file']) || strlen($_POST['config_create_file']) < 1 )
{
    die('FATAL ERROR: This script has failed!  Please report this error if it continues!');
}
$size = strlen($_POST['config_create_file']);
header("Content-Disposition: attachment; filename = config.php");
header("Content-Length: $size");
header("Content-Type: application/x-httpd-php");
echo $_POST['config_create_file'];
exit();
