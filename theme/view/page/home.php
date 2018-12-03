<?php
// Security Check for single file include
if( count( get_included_files() )==1 ) exit( "Direct access not permitted." );
$HTML.='
	<div id="container">
		<div class="box actions-box">
			<form action="" method="post">
				<input name="CreateRace" value="create race" type="submit">
			</form>
			<form action="" method="post">
				<input name="Progress" value="progress" type="submit">
			</form>
		</div>
		<div class="box race-box">
			<h1>Active Races</h1>
			<div class="active-race-table">
				<div class="race-row">
					<div class="race-cell race-number">Race (#)</div>
					<div class="race-cell center race-distance">Distance Covered (m)</div>
					<div class="race-cell center race-position">Horse Position (#)</div>
					<div class="race-cell center race-time">Time (s)</div>
				</div>
';
if($Class->Data["Active"]){
	foreach ($Class->Data["Active"] as $Race=>$Data) {
		$HTML.='
			<div class="race-row">
				<div class="race-cell race-number">'.$Race.'</div>
				<div class="race-cell center race-distance">'.$Data->Distance.'</div>
				<div class="race-cell center race-position">'.$Data->Position.'</div>
				<div class="race-cell center race-time">'.$Data->Time.'</div>
				<div class="race-cell center race-actions" data-id="race'.$Race.'">Click</div>
			</div>
		';
		$i=1;
		foreach ($Data->Details as $Horse=>$Details) {
			$HTML.='
				<div class="race-row details race'.$Race.'">
					<div class="race-cell horse-number">Horse #'.$Details->HorseNumber.'</div>
					<div class="race-cell center horse-distance">'.$Details->Distance.'</div>
					<div class="race-cell center horse-position">'.$i.'</div>
				</div>
			';
			$i++;
		}
	}
}
$HTML.='	</div>
		</div>
		<div class="box last-race-box">
			<h1>Last 5 Races</h1>
			<div class="active-race-table">
				<div class="race-row">
					<div class="race-cell race-number">Race (#)</div>
					<div class="race-cell center race-top-one">Top 1 <span>(Horse/Time)</span></div>
					<div class="race-cell center race-top-two">Top 2 <span>(Horse/Time)</span></div>
					<div class="race-cell center race-top-three">Top 3 <span>(Horse/Time)</span></div>
				</div>
';
if($Class->Data["Last"]){
	foreach ($Class->Data["Last"] as $Race=>$Data) {
		$HTML.='
			<div class="race-row">
				<div class="race-cell race-number">'.$Race.'</div>
				<div class="race-cell center race-top-one">#'.$Data->TopOne.'s</div>
				<div class="race-cell center race-top-two">#'.$Data->TopTwo.'s</div>
				<div class="race-cell center race-top-three">#'.$Data->TopThree.'s</div>
			</div>
		';
	}
}
$HTML.='
			</div>
		</div>
		<div class="box best-box">
			<h1>Best Horses</h1>
			<div class="active-race-table">
				<div class="race-row">
					<div class="race-cell race-number">Position</div>
					<div class="race-cell race-number">Horse (#)</div>
					<div class="race-cell center race-top-one">Best Time</div>
					<div class="race-cell center race-top-two">Speed</div>
					<div class="race-cell center race-top-three">Strenght</div>
					<div class="race-cell center race-top-three">Endurance</div>
				</div>
';
if($Class->Data["BestHorse"]){
	$i=1;
	foreach ($Class->Data["BestHorse"] as $Data) {
		$HTML.='
			<div class="race-row">
				<div class="race-cell race-number">'.$i.'</div>
				<div class="race-cell race-number">'.$Data->HorseNumber.'</div>
				<div class="race-cell center race-best-time">'.$Data->TotalTime.'</div>
				<div class="race-cell center race-speed">'.$Data->SpeedStat.'</div>
				<div class="race-cell center race-strenght">'.$Data->StrenghtStat.'</div>
				<div class="race-cell center race-endurance">'.$Data->EnduranceStat.'</div>
			</div>
		';
		$i++;
	}
}
$HTML.='
			</div>
		</div>
	</div>
';
?>
