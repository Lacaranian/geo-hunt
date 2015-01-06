<?php
function RAD($x) {
    return $x * pi() / 180.0;
}

function distanceBetween($p1, $p2) {
    $lat1 = RAD($p1->coords->latitude);
    $lon1 = RAD($p1->coords->longitude);
    $lat2 = RAD($p2->coords->latitude);
    $lon2 = RAD($p2->coords->longitude);

    $d = 2.0 * asin(sqrt(pow(sin(($lat1 - $lat2) / 2.0), 2.0) + cos($lat1) * cos($lat2) * pow(sin(($lon1 - $lon2) / 2.0), 2.0)));
    return $d * 20925524.9;
}

ob_start();
?>
[
	{
	    "name": "Location Name",
	    "coords": {
		"latitude": 12.123456,
		"longitude": -100.000000
	    },
	    "min_distance": 500.0,
	    "hint": "clue_name",
	    "reveal": "clue_to_reveal_on_reaching_location",
	    "source": "music_track_to_play"
	}
]
<?php
$clues = json_decode(ob_get_clean());

$pos = null;
if(isset($_REQUEST["latitude"]) && isset($_REQUEST["longitude"])){
	$pos = new stdClass();
	$pos->coords = new stdClass();
	$pos->coords->latitude = $_REQUEST["latitude"];
	$pos->coords->longitude = $_REQUEST["longitude"];
}

if(isset($_REQUEST["i_am_a_cheater"])){
	if(array_key_exists($_REQUEST["i_am_a_cheater"], $clues)){
		$pos = $clues[$_REQUEST["i_am_a_cheater"]];
	}else{
		header("Content-Type: text/plain");
		print "Choose a clue number:\n";
		foreach($clues as $number => $clue){
			print $number.": ".json_encode($clue)."\n";
		}
	}
}
	
if($pos !== null){
	$closest_clue = null;
	$closest_clue_distance = -1;
	foreach($clues as $clue){
	    $distance = distanceBetween($clue, $pos);
	    if ($closest_clue === null || $distance < $closest_clue->distance) {
		$closest_clue = $clue;
		$closest_clue->distance = $distance;
	    }
	}
	if($closest_clue != null){
		header("Content-Type: application/json");
		print json_encode($closest_clue);
	}
}
	

