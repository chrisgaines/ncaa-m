<?php

require 'vendor/autoload.php';

use Carbon\Carbon;

// The season starts on 11/13. Start there and increment by a day until we reach march 13th.
date_default_timezone_set('America/New_York');

// Set the start date.
$date = Carbon::createFromDate('2015','11','13');
// $date = Carbon::createFromDate('2015','12','03');

$season_end = Carbon::createFromDate('2016','03','13');

while ($date->lte($season_end))
{
	$games_html = file_get_contents('http://www.cbssports.com/collegebasketball/scoreboard/div1/' . $date->format('Ymd'));

	$dom = new DOMDocument;
	@$dom->loadHtml($games_html);

	$xpath = new DOMXpath($dom);

	$elements = $xpath->query("//td/@id[starts-with(., 'links')]/..");

	foreach ($elements as $element)
	{
		// Get the game id from cbs.
		$game_id = str_replace('links', '', $element->getAttribute('id'));
		
		// Figure out what URL we are about to go get
		$url = str_replace('boxscore', 'shotchart', 'http://cbssports.com' . $element->getElementsByTagName('a')->item(0)->getAttribute('href'));
		
		$game_html = file_get_contents($url);

		// Now that we have the HTML lets find the starting location for the shot chart data.
		$starting_location = strpos($game_html, 'var currentShotData = new String("');
		$ending_location = strpos($game_html, '");', $starting_location);

		$shot_string = substr($game_html, $starting_location + strlen('var currentShotData = new String("'), $ending_location - ($starting_location + strlen('var currentShotData = new String("')));

		// Do we have any shots for this game?
		if (empty($shot_string))
		{
			// Unfortunately not. Lets skip this game.
			continue;
		}

		$shot_data = explode('~', $shot_string);

		$shots = [];

		// They have it formatted pretty crappy. Do some more exploding to get a nice array.
		foreach ($shot_data as $shot)
		{
			$shots[] = explode(',',$shot);
		}

		$players = [];

		// Perfect. Now lets get all the player data.
		$starting_location = strpos($game_html, 'var playerDataHomeString = new String("');
		$ending_location = strpos($game_html, '");', $starting_location);

		$home_string = substr($game_html, $starting_location + strlen('var playerDataHomeString = new String("'), $ending_location - ($starting_location + strlen('var playerDataHomeString = new String("')));

		$home_players = explode('|', $home_string);
		
		foreach ($home_players as $player)
		{
			// Okay so this was kind of confusing. The id is succeeded by a :. So we want to get that to create a good array for storage.
			$data = explode(':', $player);

			$players[$data[0]] = explode(',', $data[1]);
		}

		// Now repeat it for the away team.
		$starting_location = strpos($game_html, 'var playerDataAwayString = new String("');
		$ending_location = strpos($game_html, '");', $starting_location);

		$away_string = substr($game_html, $starting_location + strlen('var playerDataAwayString = new String("'), $ending_location - ($starting_location + strlen('var playerDataAwayString = new String("')));

		$away_players = explode('|', $away_string);

		foreach ($away_players as $player)
		{
			// Okay so this was kind of confusing. The id is succeeded by a :. So we want to get that to create a good array for storage.
			$data = explode(':', $player);

			$players[$data[0]] = explode(',', $data[1]);
		}

		$json = json_encode([
			'date' => $date->format('Y-m-d'),
			'shots' => $shots,
			'players' => $players
		]);

		file_put_contents('shots/' . $game_id . '.json', $json);
	}

	// On to the next day!
	$date = $date->addDay();
}