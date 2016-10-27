<?php
if(isset($_GET['route'])){
	ini_set('max_execution_time',0);
	header('Content-Type: application/json');
	require_once('includes.php');

	if(function_exists($_GET['route'])){
		// sleep(3);
		echo Utils::to_json($_GET['route']());
	} else {
		echo Utils::to_json([
			"status" => 0,
			"message" =>  "You have reached here because you're finding something"
		]);
	}
} else {
	$q = @get_random_quote();
	?>
	<title>Quotes</title>
	<style>
		div {
			text-align:center;
			width:50%;
			margin-top:15%;
			padding-top:50px;
			padding-bottom:50px;
			padding-left:20px;
			padding-right:20px;
			font-size:25px;
			display:block;
			background:rgba(255,255,255,0.67);
		}
		div cite {
			font-size:15px;
			text-align:right;
		}
		body {
			background-image:url('http://lorempixel.com/1024/768/');
			background-size:cover;
			background-color:black;
		}
	</style>
		<center>
			<div>
				<blockquote><?=@$q->{'quoteText'}?></blockquote>
				<cite><?='-' . @$q->{'quoteAuthor'}?></cite>
			</div>
		</center>
	<script>
		setTimeout(function(){
			window.location.href = "./";
		}, 30000);
	</script>
	<?php
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
	$direct = (Request::input('direct')) ? +Request::input('direct') : false;
	$episode_id = Request::input('episode_id');
	$anime_id = Request::input('anime_id');

	// $episodes = Cache::remember('get_episode_videos_' . $episode_id, function() use ($episode_id, $direct){
	// 	return Animania::getEpisodeVideos($episode_id, $direct);
	// });
	$episodes = Cache::remember('get_episodes_' . $anime_id, function() use ($anime_id){
		return Animania::getAnimeEpisodes($anime_id);
	});

	foreach($episodes->episode as $key=>$episode) {
		$cacheKey = 'get_videos_' . $episode->id . '_' . (($direct) ? 'direct' : 'watch');

		$videos = Cache::remember($cacheKey, function() use ($episode, $direct){
			return Animania::getEpisodeVideos($episode->id, $direct);
		});

		$episodes->episode[$key]->{'videos'} = $videos;
	}

	$details = get_anime_details($anime_id);
	$details->{'episodes'} = $episodes->episode;
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

function get_random_quote(){
	$url = "https://crossorigin.me/http://api.forismatic.com/api/1.0/?method=getQuote&format=json&lang=en";
	$quote = file_get_contents($url);
	return json_decode($quote);
}