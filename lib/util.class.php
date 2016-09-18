<?php

class Utils {
	private function __construct(){}
	public static function to_json($obj){
		return json_encode($obj);
	}
}