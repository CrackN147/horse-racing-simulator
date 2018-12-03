<?php
// Security Check for single file include
if( count( get_included_files() )==1 ) exit( "Direct access not permitted." );
/**
 * The directory in which your View specific resources are located.
 *
 * @link Documentation/Theme/View/
 */
$View = 'view';
/**
 * The directory in which your Application are located.
 *
 * @link Documentation/Theme/View/Application
 */
$Page = 'page';
/**
 * The directory in which the Block resources are located.
 *
 * @link Documentation/Theme/View/Block
 */
$Block = 'block';
/**
 * The directory in which the Styles resources are located.
 *
 * @link Documentation/Theme/Styles/
 */
$Styles = 'style';
/**
 * The directory in which the Scripts resources are located.
 *
 * @link Documentation/Theme/Scripts
 */
$Scripts = 'script';

// Define the absolute path to the directory root View
if ( is_dir(THEME.$View) )
    define('VIEW', realpath(THEME.$View).DIRECTORY_SEPARATOR);
// Define the absolute path to the directory root Styles
if ( is_dir(THEME.$Styles) )
    define('STYLES', realpath(THEME.$Styles).DIRECTORY_SEPARATOR);
    // Define the absolute path to the directory root JS
if ( is_dir(THEME.$Scripts) )
    define('SCRIPTS', realpath(THEME.$Scripts).DIRECTORY_SEPARATOR);

// Define the absolute path to the directory root Page
if ( is_dir(VIEW.$Page) )
    define('PAGE', realpath(VIEW.$Page).DIRECTORY_SEPARATOR);
// Define the absolute path to the directory root Block
if ( is_dir(VIEW.$Block) )
    define('BLOCK', realpath(VIEW.$Block).DIRECTORY_SEPARATOR);
// Define the absolute path to the directory root CSS
if ( is_dir(STYLES) )
    define('CSS', 'theme/style/');
if ( is_dir(SCRIPTS) )
    define('JS', 'theme/script/');
// Clean up the vars
unset($View, $Page, $Block, $Styles ,$Scripts);



// Html head part
$HTML='';
$HTML.='
<!DOCTYPE html>
<html>
<head>
    <base href="'.$Class->ServerRoot().'" target="_self" />
    <title>'.$ConfHeadTitle.'</title>
';
// CSS Files control
include_once LIBRARY."css".EXT;
$HTML.='
</head>
<body>
';
// Page Structure controll
if( $Structure = $Class->GetPageStructure() ){
    $Structure = explode(',', $Structure);
    foreach ($Structure as $key => $value) {
        include_once $value;
    }
}
// JavaScript Files control
include_once LIBRARY."js".EXT;
$HTML.='
</body>
</html>
';
print $HTML;
?>