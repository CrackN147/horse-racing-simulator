<?php
// Define common error messages
define("ErrorOnAccess", "User access violation.");
define("ErrorOnDatabase", "Connection to Database faled: ");
define("ErrorOnClassLoad", "Core Class load faled: ");
// Security Check for single file include
if( count( get_included_files() )==1 ) exit( "Direct access not permitted." );
/**
 * MySQL connection variables
*/
$ConfDatabaseHost = "localhost";
$ConfDatabaseName = "task";
$ConfDatabaseUser = "root";
$ConfDatabasePass = "";
/**
 * Current directory where project files are located regarding webserver
 * !Important to know that: 
 * May cause rooting errors if not set properly
 * Case sensitive
*/
$ConfProjectDirectory	=	"";
/**
 * FrontEnd Action or Url list array 
 * !Important to know that: 
 * First array parameter in list will be used by default
 * Case sensitive
*/
$ConfActionList	=
	array( 
		1	=>	'home'
	);
/**
 * BackEnd Action or Url list array 
 * !Important to know that: 
 * First array parameter in list will be used by default
 * Case sensitive
*/
$ConfPageStructure	=
	array( 
		"App"=>array(
			// "header"		=>	array(1),
			"content"		=>	array(1),
			// "footer"		=>	array(1)
		)
	);
/**
 *  Core variables list
 * !Important to know that: 
 * First array parameter in list will be used by default
 * Case sensitive
*/
$ConfVariableList	=
	array( 
		'FirstArg' 
		// 'SecondArg', 
		// 'ThirdArg', 
		// 'ForthArg',
		// 'FifthArg'
	);
/**
 * FrontEnd Default Simple Page Name
*/
$ConfDefaultPage = "Page";
/**
 * Website head title tag
*/
$ConfHeadTitle = "Horse Racing Simulator";

?>