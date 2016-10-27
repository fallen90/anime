<?php

class Animania {

	private static $API_BASE_URL = 'api.animeplus.tv';
	public static $API_RESC_URL = 'www.animeplus.tv';
	
	private function __construct(){}

	public static function getList(){
		$endpoint = 'GetAllShows';
		return json_decode(self::getEndPoint($endpoint));
	}
	public static function getAnimeEpisodes($anime_id){
		$endpoint = 'GetDetails/' . $anime_id;
		return json_decode(self::getEndPoint($endpoint));
	}
	public static function getEpisodeVideos($episode_id, $direct=false){
		$endpoint = 'GetVideos/' . $episode_id . (($direct) ? '?direct' : '');
		$videos_raw = json_decode(self::getEndPoint($endpoint));
		$videos = [];
		if(!$direct){
			foreach($videos_raw as $v){
				$videos[] = $v->url;
			}
		} else {
			foreach($videos_raw[0] as $v){
				$videos[] = $v->link;
			}
		}
		return (array_values(array_unique($videos)));
	}
	private static function getEndPoint($endpoint){
		$url = 'http://' . self::$API_BASE_URL . '/' . $endpoint;
		return self::curl($url);
	}

	private function curl($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$headers = [
		    'App-Version:7.8',
			'App-Name:#Animania',
			'App-LandingPage:http://www.mobi24.net/anime.html',
			'Host:api.animeplus.tv',
			'Connection:Keep-Alive',
			'Content-Type:application/json',
			'User-Agent:okhttp/2.3.0'
		];

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$server_output = curl_exec ($ch);

		curl_close ($ch);

		return $server_output ;
	}
}