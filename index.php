<?php
ini_set('max_execution_time',0);
header('Content-Type: application/json');

require_once('includes.php');

if(function_exists($_GET['route'])){
	// sleep(3);
	echo Utils::to_json($_GET['route']());
}

function get_list(){
	$page = Request::input('page');
	$per_page = Request::input('per_page');

	$anime_list = Cache::remember('get_list', function(){
		return Animania::getList();
	});

	return Paginate::create($anime_list, $per_page, $page);
}

function get_anime(){
	$anime_id = Request::input('id');
	$episodes = Cache::remember('get_episodes_' . $anime_id, function() use ($anime_id){
		return Animania::getAnimeEpisodes($anime_id);
	});
	$details = get_anime_details($anime_id);
	return Cache::remember('get_anime_details_' . $anime_id, function() use($details, $episodes){
		return array_merge((array)$details, (array)$episodes);
	});
}

function get_episode_videos(){
	$direct = (Request::input('direct')) ? Request::input('direct') : false;
	$episode_id = Request::input('episode_id');
	$anime_id = Request::input('anime_id');
	$episodes = Cache::remember('get_episode_videos_' + $episode_id, function() use ($episode_id, $direct){
		return Animania::getEpisodeVideos($episode_id, $direct);
	});
	$details = get_anime_details($anime_id);
	$details->{'videos'} = $episodes;
	return $details;
}

function get_anime_details($id){
	$anime_list = Cache::remember('get_list', function(){
		return Animania::getList();
	});

	foreach($anime_list as $anime){
		if($anime->id == $id){
			return $anime;
		}
	}
}