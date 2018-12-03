<?php 
/**
 * The directory in which your Theme specific resources are located.
 *
 * @link Documentation/Theme
 */
$Theme = 'theme';
/**
 * The directory in which your Library are located.
 *
 * @link Documentation/Library
 */
$Library = 'library';
/**
 * The directory in which the Core resources are located.
 *
 * @link Documentation/Core
 */
$Core = 'core';
/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @link Documentation/Extention
 */
define('EXT', '.php');
/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @link Documentation/Extention
 */
define('SEPARATOR', DIRECTORY_SEPARATOR);
/**
 * Set the PHP error reporting level. If you set this in php.ini, you remove this.
 * @link Documentation/Error-Reporting
 */
error_reporting(E_ALL | E_STRICT);
/**
 * Set ini PHP error reporting .
 * @link Documentation/Error-Reporting
 */
ini_set('display_errors', 1);
// Set the full path to the directory root
define('ROOTS', realpath(dirname(__FILE__)).SEPARATOR);
// Define the absolute path to the directory root Theme
if ( is_dir(ROOTS.$Theme) )
    define('THEME', realpath(ROOTS.$Theme).SEPARATOR);
// Define the absolute path to the directory root Library
if ( is_dir(ROOTS.$Library) )
    define('LIBRARY', realpath(ROOTS.$Library).SEPARATOR);
// Define the absolute path to the directory root Core
if ( is_dir(ROOTS.$Core) )
    define('CORE', realpath(ROOTS.$Core).SEPARATOR);
// Clean up the vars
unset($Theme, $Library, $Core);
// Date Time Zone
date_default_timezone_set("Europe/Berlin");
setlocale(LC_TIME, 'de_DE', 'de_DE.UTF-8');
// Headers: in case of no need should be inactive
// Cache Control
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Access Control Origin
header('Access-Control-Allow-Origin: ' . $_SERVER['SERVER_NAME']);
// Content Type
header('Content-Type: text/html; charset=UTF-8');
// Sessions Cache
session_cache_limiter('private_no_expire');
// Session start
session_start();
// GZIP Handler
// ob_start("ob_gzhandler");
ob_start();
// Loader File
require CORE.'loader'.EXT;
// var_dump($Class);
include_once THEME.'compiler'.EXT;
ob_end_flush();
?>