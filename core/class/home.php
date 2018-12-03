<?php
// Security Check for single file include
if( count( get_included_files() )==1 ) exit( "Direct access not permitted." );

class Home extends Model{

	private $HorseBaseSpeed = 5.0;
	private $JockeyBaseSlowSpeed = 5.0;
	private $Distance = 1500;
	private $HorseStrenghtFactor = 8/100;
	private $ProgressSpeedFactor = 10;
	private $RoundPrecision  = 1;

	private $Horses;
	private $Stats;
	private $RaceTime;
	private $HorseDistance;

	public $Data;

	/**
     * Handle Post data for CreateRace And Progress
     *
     * 
     * 
     */
	public function CheckPost(){
		if( isset($_POST) && !empty($_POST) ){
			$StartDate = date("Y-m-d H:i:s");
        	if( isset( $_POST['CreateRace'] ) && !empty( $_POST['CreateRace'] ) ){

        		$CountActive = $this->CountRows(
        			"races",
        			"endDate >= DATE_ADD('".$StartDate."', INTERVAL factor*".$this->ProgressSpeedFactor." SECOND)"
        		);
        		// exit();
        		if( $CountActive < 3 ){
        			$this->GenerateHorses();
        			$this->GenerateRaceResults();
					
        			$FinishDate = date("Y-m-d H:i:s", (strtotime($StartDate) + $this->RaceTime) );

        			$CreateRace = $this->InsertRecordUni(
        				'races',
        				'id, factor, startDate, endDate, horses',
        				"(NULL, 0, '".$StartDate."', '".$FinishDate."', '".json_encode($this->Horses)."')"
        			);
        		}
        	}
        	elseif( isset( $_POST['Progress'] ) && !empty( $_POST['Progress'] ) ){
        		$Update = $this->UpdateRecordsUni(
        			'races',
        			'factor=factor+1',
        			"endDate >= DATE_ADD('".$StartDate."', INTERVAL factor*".$this->ProgressSpeedFactor." SECOND)"
        		);
        	}

        	unset($_POST);
        	$this->redirect();
        	exit();
        }
	}

	/**
     * Generate Random Horses With Stats
     *
     * 
     * 
     */
	public function GenerateHorses(){
		$HorseID = $this->CountRows("races","");
		$this->Horses = array();
		for ($i=1; $i < 9; $i++) {
			$HorseNumber = (int)( ( $HorseID!=0 ? $HorseID : '' ).$i);
			$this->Horses{$i} = (object)[
				"HorseNumber"=> $HorseNumber,
				"SpeedStat" => $this->HorseBaseSpeed + $this->GetRandomStat(0,5,1),
				"StrenghtStat" => $this->GetRandomStat(0,10,1), 
				"EnduranceStat" => $this->GetRandomStat(0,10,1)
			]; 
		}
	}

	/**
     * Find out Distance based on Passed time
     *
     * 
     * 
     */
	public function HorseCurrentDistance($TimePassed = 0, $Best = false){
		$Best = array( "Distance"=>0, "Position"=>0 );
		$this->HorseDistance = array();
		for ($i=1; $i < 9; $i++) {
			$Horse = $this->Horses->$i->HorseNumber;
			$Stat = $this->Stats{ $Horse };

			if( $Stat->FastTime >= $TimePassed ){
				$Distance = round( $TimePassed * $this->Horses->$i->SpeedStat ,$this->RoundPrecision );
			}
			else{
				$Distance = round( $Stat->FastDistance + ($TimePassed - $Stat->FastTime) * $Stat->SlowSpeed ,$this->RoundPrecision );
			}

			$this->HorseDistance{$Horse} = (object)[
				"HorseNumber"=> $Horse,
				"Distance" => ( $Distance < $this->Distance ? $Distance : $this->Distance)
			];

			if( $Distance > $Best["Distance"] ){ 
				$Best["Distance"] = $Distance; 
				$Best["Position"] = $Horse;
			}
		}

		usort($this->HorseDistance, function($a, $b) {
			if($a->Distance == $b->Distance){ return 0 ; }
			return ($a->Distance > $b->Distance) ? -1 : 1;
		});

		if($Best){
			return $Best;
		}
	}

	/**
     * Predict final results and Set it into object
     *
     * 
     * 
     */
	public function GenerateRaceResults() {
		$this->Stats = array();
		$this->RaceTime = 0;

		foreach ($this->Horses as $Horse) {
			$FastDistance = $Horse->EnduranceStat * 100;
			$SlowSpeed = $this->JockeyBaseSlowSpeed - ($Horse->StrenghtStat * $this->HorseStrenghtFactor);

			$FastTime = round( $FastDistance / $Horse->SpeedStat ,$this->RoundPrecision );
			$SlowTime = round( ($this->Distance - $FastDistance) / $SlowSpeed ,$this->RoundPrecision );

			$TotalTime = $FastTime + $SlowTime;
			if ($TotalTime > $this->RaceTime) { $this->RaceTime = $TotalTime; }

			$this->Stats{$Horse->HorseNumber} = (object)[
				"HorseNumber" => $Horse->HorseNumber,
				"SpeedStat" => $Horse->SpeedStat,
				"StrenghtStat" => $Horse->StrenghtStat,
				"EnduranceStat" => $Horse->EnduranceStat,
				"FastDistance" => $FastDistance,
				"SlowSpeed" => $SlowSpeed,
				"FastTime" => $FastTime,
				"SlowTime" => $SlowTime,
				"TotalTime" => $TotalTime
			];
		}
	}

	/**
     * Float Random number generator
     *
     * 
     * 
     */
	public function GetRandomStat($Min, $Max, $Decimal = 0) {
		$Scale = pow(10, $Decimal);
		return mt_rand($Min * $Scale, $Max * $Scale) / $Scale;
	}

	/**
     * Home page constructor
     *
     * 
     * 
     */
	function __construct() {
        parent::__construct();

        $this->Data["Active"] = false;
		$this->Data["Last"] = false;
		$this->Data["BestHorse"] = false;

        $StartDate = date("Y-m-d H:i:s");
        
        // $ActiveRaces = $this->SelectRows("races","","endDate > '".$StartDate."'");

        $AllRaces = $this->SelectRows("races","","","id DESC");
        
        if( $AllRaces ){
        	$i=1;
        	$TotalHorsesStats = array();

        	foreach ($AllRaces as $Race) {

        		$this->Horses = json_decode($Race->horses);
        		$this->GenerateRaceResults();
        		
        		// Find Active
        		if( strtotime($Race->endDate) - ($Race->factor * $this->ProgressSpeedFactor) > strtotime($StartDate) ){
        			// Active Race
        			$TimePassed = strtotime($StartDate) - strtotime($Race->startDate) + ($Race->factor * $this->ProgressSpeedFactor);

		        	$Best = $this->HorseCurrentDistance($TimePassed);

					$this->Data["Active"]{$Race->id} = (object)[
						"Distance" => ( $Best["Distance"] < $this->Distance ? $Best["Distance"] : $this->Distance),
						"Position" => $Best["Position"],
						"Time" => $TimePassed,
						"Details" => $this->HorseDistance
					];
        		}
        		// Find Other 5 Last Race Results
        		elseif( $i<6 ){
        			// Save Stats For Best Horse
        			$TotalHorsesStats = array_merge($TotalHorsesStats, $this->Stats);
        			// Sort Stats by Total time
        			usort($this->Stats, function($a, $b) {
						if($a->TotalTime == $b->TotalTime){ return 0 ; }
						return ($a->TotalTime < $b->TotalTime) ? -1 : 1;
					});

					$this->Data["Last"]{$Race->id} = (object)[
						"TopOne" => $this->Stats[0]->HorseNumber.'/'.$this->Stats[0]->TotalTime,
						"TopTwo" => $this->Stats[1]->HorseNumber.'/'.$this->Stats[1]->TotalTime,
						"TopThree" => $this->Stats[2]->HorseNumber.'/'.$this->Stats[2]->TotalTime
					];

        			$i++;
        		}
        	}

        	usort($TotalHorsesStats, function($a, $b) {
				if($a->TotalTime == $b->TotalTime){ return 0 ; }
				return ($a->TotalTime > $b->TotalTime) ? -1 : 1;
			});

			$TotalHorsesStats = array_slice($TotalHorsesStats,-5);

			krsort($TotalHorsesStats);

        	$this->Data["BestHorse"] = $TotalHorsesStats;
			
        }
    }

}

?>