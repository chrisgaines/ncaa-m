<?php

require 'vendor/autoload.php';

use Carbon\Carbon;

// The season starts on 11/13. Start there and increment by a day until we reach march 13th.
date_default_timezone_set('America/New_York');
$date = Carbon::createFromDate('2015','11','13');

$season_end = Carbon::createFromDate('2016','03','13');

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
		$game_file_contents = file_get_contents($base_url . $url_suffix);

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

