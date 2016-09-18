<?php

class Request {
	private function __construct(){}
	public static function input($key){
		return (isset($_REQUEST[$key])) ? $_REQUEST[$key] : null;
	}
}