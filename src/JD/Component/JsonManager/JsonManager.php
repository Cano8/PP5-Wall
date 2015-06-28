<?php

namespace JD\Component\JsonManager;

class JsonManager
{
	protected $json;
	protected $address;

	public function __construct($address) {
		$this->address = $address;
		$this->json = json_decode(file_get_contents($address), true);
		return $this->json;
	}

	public function findAll() {
		$array = $this->json;

		/*
		foreach ($array as $single) {
			foreach ($single['tags'] as $tag){
				$tags .= $tag .', ';
				echo $tag;
				$tags .= $id["picture"];
			}
		}*/

		/*
		echo '<script type="text/javascript">alert('.'\'lol\''.')</script>';
		*/

		//echo '<pre>';
		//echo var_dump($array).'</pre>';

		return $array;
	}

	public function numberOfElements() {
		return count($this->json);
	}

	public function updateJson($number, $field, $newValue) {
		$this->json[$number][$field] = $newValue;
		file_put_contents($this->address, json_encode($this->json));
	}

	public function removeEntryJson($whichEntry) {
		array_splice($this->json, $whichEntry, 1);

		if ( count($this->json) == 0 ) {
			$tags = array("empty");
			$newEntry = array(
				picture  => "http://placehold.it/120x120",
				about => "empty",
				tags => $tags,
				location => "empty"
			);
			array_push($this->json, $newEntry);
		}
		file_put_contents($this->address, json_encode($this->json));
	}

	public function addEntryJson() {
		$tags = array("noTags");
		$newEntry = array(
			'picture'  => "http://placehold.it/120x120",
			'about' => "Default description",
			'tags' => $tags,
			'location' => "No location"
		);
		array_push($this->json, $newEntry);
		file_put_contents($this->address, json_encode($this->json));
	}
}
