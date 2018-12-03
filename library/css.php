<?php
// Security Check for single file include
if( count( get_included_files() )==1 ) exit( "Direct access not permitted." );
/*
  * CSS Files List!
  * CSS File Name => Array ( Condition )
  * Several scenarios possible:
  * ALL => (bool)
  * CMS => (bool)
  * Session => (name, value)
  * Page Name => (pages names array)
  * Max => (Max page number in ActionList until which to parse )
  * Min => (Min page number in ActionList from which to start )
*/
  $CSSList = array(
    // CMS
    'style.css'                                                               => array( "ALL" ),
    // 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'             => array( "ALL" )
  );


  foreach ($CSSList as $FileName => $Condition) {
    $InsertCSS = false;

    // ALL
    if(isset($Condition[0]) && $Condition[0]==="ALL"){
      $InsertCSS = true;
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
        $InsertCSS = true;
      }
    }

    // One OR Many OR CMS
    if( !$InsertCSS ){
      foreach ($Condition as $Key => $Page) {
        if( $Class->FirstArg===$Page ){
          $InsertCSS = true;
          break;
        }
      }
    }

    if( $InsertCSS ){
      $Directory = '';
      if( strpos($FileName, "https://") !== false || strpos($FileName, "http://") !== false){
        $Directory = $FileName;
      }
      elseif( is_file( CSS . $FileName ) && filesize( CSS . $FileName ) > 0 ){
        $Directory = CSS . $FileName;
      }
      if( !empty($Directory) ){
        $HTML.='<link rel="stylesheet" type="text/css" href="'. $Directory .'" />';
      }
    }

  }
?>