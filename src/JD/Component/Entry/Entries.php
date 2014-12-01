<?php

namespace JD\Component\JsonManager;
namespace JD\Component\Entry;

class Entries
{
	protected $entries;
	
	public function __construct($json)
	{
		$entries = [];
		$i = 0;
		foreach ($json as $singleEntry) {
			$entries[$i] = new Entry($i, $singleEntry);
			$i++;
		}
		$this->entries = $entries;
	}
	
	public function getEntries()
	{
		return $this->entries;
	}
}
