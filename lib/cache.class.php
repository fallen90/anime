<?php
class Cache {
	private function __construct(){}
	public static function remember($key, $callback){
		if(file_exists(self::get_cache_filename($key))){
			return self::parse_cache($key);
		} else {
			$callback_return = $callback();
			self::encode_cache($key, $callback_return);
			return $callback_return;
		}
	}

	private static function get_cache_filename($key){
		return "./cache/" . $key .'.json';
	}
	private static function parse_cache($key){
		return (json_decode(file_get_contents(self::get_cache_filename($key)), false));
	}
	private static function encode_cache($key, $data){
		return file_put_contents(self::get_cache_filename($key), json_encode($data));
	}
}