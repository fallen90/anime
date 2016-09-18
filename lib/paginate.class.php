<?php

class Paginate {
	private function __construct(){}

	public static function create($items, $items_per_page, $page){
		if($items == null){
			return [];
		}
		
		$items_per_page = ($items_per_page == null) ? 15 : $items_per_page;
		$page = ($page == null) ? 1 : $page;

		return self::transform($items, $page, $items_per_page);
	}

	private static function get_chunk_by_index($items, $index, $items_per_page){
		$chunk = array_slice($items, ($items_per_page) *  ($index-1), $items_per_page);
		return $chunk;
	}

	private static function transform($items, $page, $items_per_page){
		$page_data = self::get_chunk_by_index($items, $page, $items_per_page);
		$pages = (int) floor(sizeof($items) / $items_per_page);
		return [
			"current_page" => $page,
			"data" => $page_data,
			"last_page" => $pages,
			"per_page" => $items_per_page,
			"total" => sizeof($items)
		];
	}
}