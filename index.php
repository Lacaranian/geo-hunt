<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<style>
html {
    background-color: black;
    color: #53CF29;
    font-family:'Courier New', 'Courier';
    font-weight: bold;
}
.cursor {
    background-color: #53CF29;
    animation: blink 1400ms steps(2, start) infinite;
    -webkit-animation: blink 1400ms steps(2, start) infinite;
}
@keyframes blink {
    to {
        visibility: hidden;
    }
}
@-webkit-keyframes blink {
    to {
        visibility: hidden;
    }
}
#super_awesome_type_stuff_div {
    padding: 20px;
}
</style>
<script>
/* From http://www.kryptonite-dove.com/blog/load-json-file-locally-using-pure-javascript { */
 function loadJSON(url, callback) {
    var xobj = new XMLHttpRequest();
    xobj.open('GET', url, true); // Replace 'my_data' with the path to your file
    xobj.onreadystatechange = function () {
      if (xobj.readyState == 4 && xobj.status == "200") {
        // Required use of an anonymous callback as .open will NOT return a value but simply returns undefined in asynchronous mode
        callback(JSON.parse(xobj.responseText));
      }
    };
    xobj.send(null);
 }
/* } */
 

function proto_typer(text, element_id, delay, delay_fudge, callback) {
    var cursor = "<span class=\"cursor\">&nbsp;</span>";
    var line_start = "&gt; ";
    var current_text = document.getElementById(element_id).innerHTML.replace(cursor, "");
    if (current_text === "") {
    	current_text += line_start;
    }
    var current_delay = 0;
    text.split('').forEach(function (c) {
        current_delay += delay + (Math.random() - 0.5) * delay_fudge;
        setTimeout(function () {
            if (c === '\n') {
                current_text += "<br />" + line_start;
            } else if (c === '\t') {
                current_text += "&nbsp;&nbsp;&nbsp;&nbsp;";
            } else if (c === ' ') {
                current_text += "&nbsp;";
            } else if (c === '\0') {
                current_text = line_start;
            }else{
                current_text += c;
            }
            document.getElementById(element_id).innerHTML = current_text + cursor;
        }, current_delay);
    });
    if(callback !== null){
    	setTimeout(callback, current_delay);
    }
}

var last_clue = null;
var clue_audio = new Audio();

function hackerGivesCluesForLocation(element_id, pos, callback){
	loadJSON("clues.php?latitude=" + pos.coords.latitude + "&longitude=" + pos.coords.longitude, function(closest_clue){
		if (closest_clue !== null && closest_clue.distance <= closest_clue.min_distance) {
		    if (last_clue == null || last_clue.name != closest_clue.name) {
			//Play audio
			var audio = "";
			if(clue_audio.canPlayType("audio/ogg") !== ""){
				audio = closest_clue.source + ".ogg";
			}else if(clue_audio.canPlayType("audio/mp3") !== ""){
				audio = closest_clue.source + ".mp3";
			}
			proto_typer(audio === "" ? "No audio support\n" : "play " + audio + "\n", element_id, 150, 50, function(){
				clue_audio.src = audio;
				if(audio !== ""){
					clue_audio.play();
				}
				//Type out clue
				proto_typer(closest_clue.reveal + "\n", element_id, 150, 50, callback);
			});
		    }
		}else if(closest_clue !== null){
			proto_typer("You are " + Math.round(closest_clue.distance/528)/10 + " miles away from the closest clue...\n", element_id, 150, 50, callback);
		}
		last_clue = closest_clue;
	});
}

function hackerGivesClues(element_id) {
	navigator.geolocation.getCurrentPosition(function (pos) {
		hackerGivesCluesForLocation(element_id, pos, function(){
		});
	});
}

function playAGame(element_id){
	proto_typer("Want to play a game?\n", element_id, 150, 50, function(){
		hackerGivesClues(element_id);
	});
}
</script>
</head>
<body onload="playAGame('super_awesome_type_stuff_div')">
<div id="super_awesome_type_stuff_div"></div>
</body>
</html>
