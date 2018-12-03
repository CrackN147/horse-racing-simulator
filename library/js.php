<?php
// Security Check for single file include
if( count( get_included_files() )==1 ) exit( "Direct access not permitted." );

/*
  * JS Files List!
  * JS File Name => Array ( Condition )
  * Several scenarios possible:
  * ALL => (bool)
  * CMS => (bool)
  * Session => (name, value)
  * Page Name => (pages names array)
  * Max => (Max page number in ActionList until which to parse )
  * Min => (Min page number in ActionList from which to start )
*/

$JSList = array(
    // CMS
  'https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous'                                                             => array( "ALL" ),
  'script.js'                                                                           => array( "ALL" )
	
);

$PageScript = $PHPPageScript = '';
if( isset($Class->FirstArg) && !empty($Class->FirstArg) ){
	$PageScript = $Class->FirstArg . '.js';
	$PHPPageScript = $Class->FirstArg . '.php';
}

foreach ($JSList as $FileName => $Condition) {
    $InsertJS = false;

    // ALL
    if(isset($Condition[0]) && $Condition[0]==="ALL"){
    	$InsertJS = true;
    }

    if( isset($Condition["Max"]) && !empty($Condition["Max"]) || isset($Condition["Min"]) && !empty($Condition["Min"]) ){
      if( isset($Condition["Min"]) ){ $MIN = $Condition["Min"]; }else{ $MIN = 0; }
      if( isset($Condition["Max"]) ){ $MAX = $Condition["Max"]; }else{ $MAX = 200; }
      $CurrentPosition = array_search($Class->FirstArg, $ConfActionList);
      if( $CurrentPosition && 
        $Class->FirstArg===$ConfActionList[$CurrentPosition] &&
        $CurrentPosition >= $MIN && 
        $CurrentPosition < $MAX 
      ){
        $InsertJS = true;
      }
    }
    // One OR Many OR CMS
    if( !$InsertJS ){
      foreach ($Condition as $Key => $Page) {
      	if( $Class->FirstArg===$Page ){
      		$InsertJS = true;
      		break;
      	}
   		}
    }
    
    if( $InsertJS ){
      	$Directory = '';
  		if( strpos($FileName, "https://") !== false || strpos($FileName, "http://") !== false ){
  			$Directory = $FileName;
  		}
  		elseif( is_file( JS . $FileName ) && filesize( JS . $FileName ) > 0 ){
  			$Directory = JS . $FileName;
  		}
  		if( !empty($Directory) ){
  			$HTML.='<script type="text/javascript" src="'. $Directory .'"></script>';
  		}
    }
}

if( !empty($PageScript) ){
	if( is_file( JS . $PageScript ) && filesize( JS . $PageScript ) ) {
		$HTML.='<script type="text/javascript" src="'. JS . $PageScript .'"></script>';
	}
	elseif( is_file( JS . $PHPPageScript ) && filesize( JS . $PHPPageScript ) ) {
	    print $HTML;
	    $HTML = '';
	    include_once JS . $PHPPageScript;
	}
}
?>