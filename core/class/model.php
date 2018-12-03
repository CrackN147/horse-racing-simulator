<?php
// Security Check for single file include
if( count( get_included_files() )==1 ) exit( "Direct access not permitted." );

class Model extends Controller{

    public function CustomQuery( $CustomQuery= '', $Type = '' ){
        if( !empty($CustomQuery) ){
            $Query = $CustomQuery;
            // print_r($CustomQuery);
            $Result = $this->DB->prepare($Query);
            $Result->execute();
        }
        if ($Result) {
            if( $Type=="object" ){
                return $Result->fetchAll(PDO::FETCH_OBJ);
            }
            elseif( $Type=="column" ){
                return $Result->fetchColumn();
            }
            else{
                return $Result->fetchAll();
            }
        }
        return false;
    }

    public function CountRows( $Table = '', $Condition= '' ){
        if( !empty($Condition) ){
            $Condition = "WHERE " . $Condition;
        }
        $Query = sprintf("SELECT COUNT(*) FROM `%s` %s" , $Table , $Condition);
        // print_r($Query);
        $Result = $this->DB->prepare($Query);
        $Result->execute();
        if ($Result) {
            return $Result->fetchColumn(0);
        }
        return false;
    }

    public function InsertRecordUni($Table='', $Fields='', $Values='') {
        if( !empty($Table) && !empty($Fields) && !empty($Values) ){
            $Query = sprintf("INSERT INTO `%s` (%s) VALUES %s", $Table, $Fields, $Values);
            // var_dump($Query);
            $Result = $this->DB->prepare($Query);
            $Result->execute();

            if ($Result) {
                return true;
            }
            return false;
        }
    }

    public function UpdateRecordsUni($Table = '', $Set = '', $Where = '') {
        if( !empty($Table) && !empty($Set) && !empty($Where) ){
            $Query = sprintf("UPDATE `%s` SET %s WHERE %s", $Table, $Set, $Where);
            // var_dump($Query);
            $Result = $this->DB->prepare($Query);
            $Result->execute();

            if ($Result) {
                return true;
            }
            return false;
        }
    }

    public function SelectRows( $Table='', $Fields = '', $Condition='', $Order = '', $Limit = '', $Distinct = '' ){
        if( !empty($Fields) ){
            if ( strpos($Fields, ',' ) !== false) {
                $Temp = explode(',', $Fields);
                $Fields='';
                foreach ($Temp as $key => $value) {
                    $Fields.="`".$value."`,";
                }
                $Fields = rtrim($Fields,',');
            }
            else{
                $Fields="`".$Fields."`";
            }
        }
        else{
            $Fields='*';
        }

        if(!empty($Condition)) { $Condition=" WHERE ".$Condition; }
        if(!empty($Order)) { $Order=" ORDER BY ".$Order; }
        if(!empty($Limit)) { $Limit=" LIMIT ".$Limit; }

        if(!empty($Distinct)) { $Distinct="DISTINCT(".$Distinct."),"; }

        $Query = sprintf("SELECT %s %s FROM `%s` %s %s %s", $Distinct, $Fields, $Table , $Condition, $Order, $Limit);
        // print_r($Query);
        $Result = $this->DB->prepare($Query);
        $Result->execute();
         
        if ($Result) {
            return $Result->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

} 
 ?>