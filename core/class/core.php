<?php
// Security Check for single file include
if( count( get_included_files() )==1 ) exit( "Direct access not permitted." );

abstract class Core{
    /**
     * Constructs url to work with. Based on default variables.
     *
     * @param   none
     * @return  defines route
     */
	public function __construct() {
        // Use of global variables set in Settings file
		global 
            $DB,
            $ConfProjectDirectory
        ;
        // Get Request URL
		$Request = $_SERVER["REQUEST_URI"];
        
        $Ext = array(".php","script");
        // Check each extention to Url
        foreach ($Ext as $key => $value) {
            if( strpos( strtolower( $Request ), $value ) !== false ){
                // If match found locate to default url
                header( "Location: ".$this -> ServerRoot() );
                exit();
            }
        }

        $QueryString = $_SERVER['QUERY_STRING'];
        $Key = array('chr(', 'chr=', 'chr%20', '%20chr', 'wget%20', '%20wget', 'wget(',
            'cmd=', '%20cmd', 'cmd%20', 'rush=', '%20rush', 'rush%20',
            'union%20', '%20union', 'union(', 'union=', 'echr(', '%20echr', 'echr%20', 'echr=',
            'esystem(', 'esystem%20', 'cp%20', '%20cp', 'cp(', 'mdir%20', '%20mdir', 'mdir(',
            'mcd%20', 'mrd%20', 'rm%20', '%20mcd', '%20mrd', '%20rm',
            'mcd(', 'mrd(', 'rm(', 'mcd=', 'mrd=', 'mv%20', 'rmdir%20', 'mv(', 'rmdir(',
            'chmod(', 'chmod%20', '%20chmod', 'chmod(', 'chmod=', 'chown%20', 'chgrp%20', 'chown(', 'chgrp(',
            'locate%20', 'grep%20', 'locate(', 'grep(', 'diff%20', 'kill%20', 'kill(', 'killall',
            'passwd%20', '%20passwd', 'passwd(', 'telnet%20', 'vi(', 'vi%20',
            'insert%20into', 'select%20', 'nigga(', '%20nigga', 'nigga%20', 'fopen', 'fwrite', '%20like', 'like%20',
            '$_request', '$_get', '$request', '$get', '.system', 'HTTP_PHP', '&aim', '%20getenv', 'getenv%20',
            'new_password', '&icq','/etc/password','/etc/shadow', '/etc/groups', '/etc/gshadow',
            'HTTP_USER_AGENT', 'HTTP_HOST', '/bin/ps', 'wget%20', 'uname\x20-a', '/usr/bin/id',
            '/bin/echo', '/bin/kill', '/bin/', '/chgrp', '/chown', '/usr/bin', 'g\+\+', 'bin/python',
            'bin/tclsh', 'bin/nasm', 'perl%20', 'traceroute%20', 'ping%20', '.pl', '/usr/X11R6/bin/xterm', 'lsof%20',
            '/bin/mail', '.conf', 'motd%20', 'HTTP/1.', '.inc.php', 'config.php', 'cgi-', '.eml',
            'file\://', 'window.open', '<SCRIPT>', 'javascript\://','img src', 'img%20src','.jsp','ftp.exe',
            'xp_enumdsn', 'xp_availablemedia', 'xp_filelist', 'xp_cmdshell', 'nc.exe', '.htpasswd',
            'servlet', '/etc/passwd', 'wwwacl', '~root', '~ftp', '.js', '.jsp', 'admin_', '.history',
            'bash_history', '.bash_history', '~nobody', 'server-info', 'server-status', 'reboot%20', 'halt%20',
            'powerdown%20', '/home/ftp', '/home/www', 'secure_site, ok', 'chunked', 'org.apache', '/servlet/con',
            '<script', '/robot.txt' ,'/perl' ,'mod_gzip_status', 'db_mysql.inc', '.inc', 'select%20from',
            'select from', 'drop%20', '.system', 'getenv', 'http_', '_php', 'php_', 'passwd', 'phpinfo()', '<?php', '?>', 'sql=', 'p=-', 'c$'
        );
        // Check Query String for Hacking attemts
        $Checking = str_replace($Key, '*', $QueryString);
        if ($QueryString != $Checking){
            // If match found locate to default url
            header( "Location: ".$this -> ServerRoot() );
            exit();
        }

        // Check if not empty project directory and remove it from Request
        if( !empty($ConfProjectDirectory) ){
            $Request = str_replace($ConfProjectDirectory, '', $Request);
        }
        // Convert Request to array
        $Request = explode("/", $Request);
        // Remove any empty array ellements
        $Request = array_values( array_filter($Request) );

        // Check if request is not empty
        if( !empty($Request) ){
            // Process all request values
            foreach ($Request as $key => $value) {
                $this->ProcessRequestValue(false,$key,$value);
            }
        }
        else{
            $this->ProcessRequestValue(true);
        }
        // Set database variable
        $this -> DB = $DB;
    }
    /**
     * Processes request value
     *
     * @param   bool $CMSRequest
     * @param   bool $SingleLanguage
     * @param   bool $EmptyRequest
     * @param   int $RequestKey
     * @param   string $RequestValue
     * @return  sets class variables
     */
    public function ProcessRequestValue($EmptyRequest = true, $RequestKey = 0, $RequestValue = ''){
        // Use of global variables set in Settings file
        global 
            $ConfVariableList,
            $ConfActionList
        ;
        
        // Determines how to handle variables
        if( $EmptyRequest ){
            // Set first variable by default
            $this -> {$ConfVariableList[$RequestKey]} = $ConfActionList[1];
        }
        else{
            // Check variable before setting it
            if($RequestKey==0){
                $this -> CheckRequestVariables(1,$RequestValue);
            }
            // Set variable acordingly to list
            $this -> {$ConfVariableList[$RequestKey]} = urldecode($RequestValue);
        }
    }
    /**
     * Compare request value to allowed list and exit on fail
     *
     * @param   string $RequestCase
     * @param   string $RequestValue
     * @return  stops execution
     */
    public function CheckRequestVariables( $RequestCase = '1', $RequestValue = '' ){
        // Use of global variables set in Settings file
        global
            $ConfActionList
        ;
        // Switch requested case 
        switch ($RequestCase) {
            case '1':
                // Check for match with action list
                if ( !in_array($RequestValue, $ActionList, true) ){
                    // If match not found locate to default url
                    header( "Location: ".$this->ServerRoot() );
                    exit();
                }
            break;
        }
    }
    /**
     * Set Active argument
     * Define File Name
     *
     * @return  included php file name
     */
    public static function AssignActiveArg() {
        
        $Class->ActiveArg = $Class->FirstArg;
        // Define File Name
        $FileName = '';
        if( strpos($Class->ActiveArg, "-") !== false ){
            $ExArg = explode("-", $Class->ActiveArg);
            foreach ($ExArg as $key => $value) {
                $FileName.=ucfirst($value);
            }
        }
        else{
            $FileName = ucfirst($Class->ActiveArg);
        }
        define('FILENAME', $FileName);
    }
    /**
     * Returns server host with or without in-file directory
     *
     * @return  string
     */
    public function ServerRoot( $FullDir = true ) {
        // Determine server protocol and port
        $Protocol = 'http';
        if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')) {
            $Protocol .= 's';
            $Port = $_SERVER['SERVER_PORT'];
        } else {
            $Port = 80;
        }
        // Determine server host
        $Host = $_SERVER['HTTP_HOST'];
        $Host = $Protocol ."://" . $Host;
        $Host = str_replace("www.www", "www", $Host);
        // Determine and clean current script directory
        $Directory = str_replace( $_SERVER['PHP_SELF'], '', preg_replace( '/([\w.]+)$/', '', $_SERVER['SCRIPT_NAME'] ) );
        // Return host and directory
        if($FullDir){
            return $Host.$Directory;
        }
        else{
            return $Host;
        }
    }
}
?>