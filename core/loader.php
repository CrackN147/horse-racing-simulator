<?php
// Security Check for single file include
if( count( get_included_files() )==1 ) exit( "Direct access not permitted." );
/**
 * The directory in which the Data resources are located.
 *
 * @link Documentation/Config
 */
$Data = 'config';

/**
 * The directory in which the Classes resources are located.
 *
 * @link Documentation/class
 */
$Classes = 'class';

// Define the absolute path to the directory root Data
if ( is_dir(CORE.$Data) )
    define('DATA', realpath(CORE.$Data).SEPARATOR);

// Define the absolute path to the directory root  Classes
if ( is_dir(CORE.$Classes) )
    define('CLASSES', realpath(CORE.$Classes).SEPARATOR);

// Clean up the vars
unset($Data, $Classes, $UserClasses);

// Settings File
require DATA."settings".EXT;

// MySQL connection testing and cooonection via PDO
try {
    $DB = new PDO(
        'mysql:host='.$ConfDatabaseHost.';dbname='.$ConfDatabaseName.';charset=utf8', $ConfDatabaseUser, $ConfDatabasePass
    );
    $DB->setAttribute(
        PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION
    );
    $DB->setAttribute(
        PDO::ATTR_EMULATE_PREPARES, false
    );
    $DB->exec("set names utf8");
}
catch( PDOException $ex ) {
    exit( ErrorOnDatabase.$ex->getMessage());
}

// Include all class files with native autoload function from directory
// function __autoload( $Class ) { /// Deprecated since 7.2
spl_autoload_register( function ( $Class ) {
    try {
        if (file_exists(CLASSES.$Class.EXT)) {
            require_once (CLASSES.$Class.EXT);
        }
    } catch ( Exception $e ) {
        print( ErrorOnClassLoad.$e->getMessage() );
        exit();
    }
});

// Default variable for Class
$Class = new Home();

$Class->CheckPost();

?>