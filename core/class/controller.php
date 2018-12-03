<?php
// Security Check for single file include
if( count( get_included_files() )==1 ) exit( "Direct access not permitted." );

class Controller extends Core{
    /**
     * Get structure for current page
     *
     * @return  list of structurised files
     */
    public function GetPageStructure() {
        global
            $ConfPageStructure,
            $ConfActionList
        ;

        if( !empty($ConfPageStructure["App"]) ){
            $StructureList = '';
            foreach ($ConfPageStructure["App"] as $Name => $Condition) {
                // Page
                if( $Name === 'content' && $Page = $this->GetPageContent() ){
                    $StructureList.=$Page.',';
                }
                // Block
                if( $Block = $this->GetPageBlocks($Name) ){
                    if( isset($Condition["Max"]) && !empty($Condition["Max"]) || 
                        isset($Condition["Min"]) && !empty($Condition["Min"]) 
                    ){
                        if( isset($Condition["Min"]) ){ $MIN = $Condition["Min"]; }else{ $MIN = 0; }
                        if( isset($Condition["Max"]) ){ $MAX = $Condition["Max"]; }else{ $MAX = 200; }
                        $CurrentPosition = array_search($this->FirstArg, $ConfActionList);

                        if( $CurrentPosition && 
                            $this->FirstArg===$ConfActionList[$CurrentPosition] &&
                            $CurrentPosition >= $MIN && 
                            $CurrentPosition < $MAX 
                        ){
                            $StructureList.=$Block.',';
                        }
                    }
                    // One OR Many
                    else{
                        foreach ($Condition as $PageNumber => $PageName) {
                            if( $this->FirstArg===$PageName ){
                                $StructureList.=$Block.',';
                                break;
                            }
                        }
                    }
                }
            }
            return rtrim($StructureList,',');
        }   
    }
    /**
     * Get needed file name for current page
     *
     * @return  included php file name
     */
    public function GetPageContent() {
        global
            $ConfDefaultPage
        ;
        $FileName = $this->FirstArg;
        if( strpos($this->FirstArg, "-") !== false ){
            $FileName = '';
            $ExArg = explode("-", $this->FirstArg);
            foreach ($ExArg as $key => $value) {
                $FileName.=ucfirst($value);
            }
        }

        if ( file_exists(PAGE.$this->FirstArg.EXT) ) {
            return PAGE.$this->FirstArg.EXT;
        }
        else if ( file_exists(PAGE.ucfirst($ConfDefaultPage).EXT) ) {
            return PAGE.ucfirst($ConfDefaultPage).EXT;
        }
    }
    /**
     * Get needed blocks for current page
     *
     * @return  included php file name blocks
     */
    public function GetPageBlocks($BlockName = '') {
        if(!empty($BlockName)){
            $FileName= '';
            if( strpos($BlockName, "-") !== false ){
                $ExArg = explode("-", $BlockName);
                foreach ($ExArg as $key => $value) {
                    $FileName.=ucfirst($value);
                }
            }
            else{
                $FileName = ucfirst($BlockName);
            }

            if ( file_exists(BLOCK.ucfirst($FileName).EXT) ) {
                return BLOCK.ucfirst($FileName).EXT;
            }
            elseif( file_exists(BLOCK.$FileName.EXT) ){
                return BLOCK.$FileName.EXT;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    public function redirect($Url='', $Mode = 'php', $Target = true){
        switch ( $Mode ) {
            case 'php':
                header("Location:".$this->ServerRoot().$Url);
                exit();
            break;
            case 'timeout':
                $Content='
                    <script>
                        window.setTimeout(function() {
                            window.location = \''.$this->ServerRoot().$Url.'\';
                          }, 5000);
                    </script>
                ';
                print $Content;
                
            break;
            case 'js':
                $Content='';
                if( $Target ){
                    $Content.='
                        <script language="javascript">
                            document.location = \''.$Url.'\';
                        </script>
                    ';
                }
                else{
                    $Content.='
                        <script language="javascript">
                            window.open(\''.$Url.'\');
                        </script>
                    ';
                }
                print $Content;
                exit();
            break;
        }
    }

}
?>