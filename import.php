<?php

require 'vendor/autoload.php';

use Carbon\Carbon;

// The season starts on 11/13. Start there and increment by a day until we reach march 13th.
date_default_timezone_set('America/New_York');
$date = Carbon::createFromDate('2015','11','13');

$season_end = Carbon::createFromDate('2016','03','13');

// We want to track failed games.
// $failed_games = [];
$failed_games = [
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/13/university-of-the-southwest-abilene-christian/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/13/drexel-saint-josephs/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/13/portland-st-grand-canyon/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/13/texas-lutheran-incarnate-word/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/14/grambling-mid-atlantic-christ/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/14/fgcu-ohio/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/15/niagara-saint-josephs/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/16/texas-wesleyan-fgcu/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/16/schreiner-incarnate-word/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/17/tiffin-northern-ky/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/17/howard-payne-abilene-christian/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/17/black-hills-st-grand-canyon/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/18/sacred-heart-mass-lowell/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/18/seattle-eastern-wash/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/18/loyola-maryland-umbc/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/18/buffalo-saint-josephs/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/19/alcorn-st-grand-canyon/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/20/fla-atlantic-northeastern/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/21/florida-saint-josephs/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/21/morehead-st-northern-ky/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/21/youngstown-st-fgcu/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/22/old-dominion-saint-josephs/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/22/youngstown-st-bowling-green/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/22/ball-st-south-carolina-st/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/22/north-dakota-fgcu/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/23/central-ark-utsa/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/23/howard-texas-southern/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/23/bowling-green-fgcu/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/23/mississippi-val-grand-canyon/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/24/wheelock-mass-lowell/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/27/fgcu-florida/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/28/oakland-abilene-christian/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/29/cornell-mass-lowell/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/29/ave-maria-fgcu/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/29/mercer-western-mich/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/11/30/hampton-grand-canyon/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/12/01/villanova-saint-josephs/boxscore.json',
	'http://data.ncaa.com/jsonp/game/basketball-men/d1/2015/12/02/fgcu-texas-am/boxscore.json'
];

while ($date->lte($season_end))
{
	// Create the json string.
	$file_contents = file_get_contents('http://data.ncaa.com/jsonp/scoreboard/basketball-men/d1/' . $date->format('Y') . '/' . $date->format('m') . '/' . $date->format('d') . '/scoreboard.json');

	// Remove the callback function from the data.
	$data = json_decode(str_replace('callbackWrapper(', '', str_replace(');', '', $file_contents)),true);
	
	// Set the base url to append the game url to.
	$base_url = 'http://data.ncaa.com/jsonp/';

	// Loop through the games for this day and download the json files.
	foreach($data['scoreboard'][0]['games'] as $key => $game)
	{
		// remove the part of the url we don't need.
		$url_suffix = str_replace('/sites/default/files/data/', '', str_replace('gameinfo', 'boxscore', $game));

		// Get the data for this game.
		try {
			$game_file_contents = file_get_contents($base_url . $url_suffix);
		} catch (\ErrorException $e) {
			// An error occured while trying to get the game data.
			$failed_games[] = $base_url . $url_suffix;
		}
		

		// Get rid of the callback function.
		$data = json_decode(str_replace('callbackWrapper(', '', str_replace(');', '', $game_file_contents)),true);

		// Save the json to a file.
		file_put_contents('games/' . $date->format('Ymd') . '_' . ($key + 1) . '.json', $data);

		// Pause the script for 3 seconds before we move on to the next game.
		sleep(3);
	}

	// On to the next day!
	$date = $date->addDay();
}

// Now that we have finished getting game data for this season, lets make sure we track which games failed.
file_put_contents('games/failed.json',$failed_games);

echo 'Finished importing.';
