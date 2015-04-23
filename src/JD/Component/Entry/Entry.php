<?php

namespace JD\Component\JsonManager;
namespace JD\Component\Entry;

class Entry
{
	protected $number;
	protected $picture;
	protected $about;
	protected $tags;
	protected $location;
	
	public function __construct($number, $entry) {
		$this->number = $number;
		$this->picture = $entry['picture'];
		$this->about = $entry['about'];
		$this->tags = $entry['tags'];
		$this->location = $entry['location'];
	}
	
	public function getNumber() {
		return $this->number;
	}
	
	public function setNumber($new) {
		$this->number = $new;
	}
	
	public function getLocation() {
		return $this->location;
	}
	
	public function setLocation($new, $json) {
		$this->location = $new;
		$json->updateJson($this->number, 'location', $new );
	}
	
	
	public function getPicture() {
		return $this->picture;
	}
	
	public function setPicture($new, $json) {
		$this->picture = $new;
		$json->updateJson($this->number, 'picture', $new );
	}
	
	public function getAbout() {
		return $this->about;
	}
	
	public function setAbout($new, $json) {
		$this->about = $new;
		$json->updateJson($this->number, 'about', $new);
	}
	
	public function getTags() {
		return $this->tags;
	}
	
	public function addTag($new, $json) {
		array_push($this->tags, 'costam');
		$json->updateJson($this->number, 'tags', $this->tags);
	}
	
	public function clearTags ($json) {
		$this->tags = array();
		$json->updateJson($this->number, 'tags', $this->tags);
	}
	
	public function getTagsString()	{
		$tagsArray = $this->tags;
		$result;
		foreach ( $tagsArray as $singleTag ) {
			$result .= $singleTag.', ';
		}
		return rtrim($result, ', ');
	}
}
