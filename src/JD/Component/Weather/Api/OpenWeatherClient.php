<?php

namespace JD\Component\Weather\Api;

use Buzz\Browser;
use \DateTime;

class OpenWeatherClient
{
	protected $browser;
	protected $url = 'http://api.openweathermap.org/data/2.5/weather';
	protected $format = 'json';
	protected $stopWatch;
	protected $fs;
	protected $cacheFile;
	
	public function __construct(Browser $browser, $fs, $cacheFile, $stopWatch)
	{
		$this->browser = $browser;
		$this->stopWatch = $stopWatch;
		$this->fs = $fs;
		$this->cacheFile = $cacheFile;
	}
	
	public function callApi($city)
	{
		$path = sprintf(
			'%s?q=%s',
			$this->url,
			$city
		);
		
		$this->stopWatch->start('callApi');
		if ($this->needToBeRefreshed()) {
			$this->refreshCache($path);
		}
		$this->stopWatch->stop('callApi');
		
		return file_get_contents($this->cacheFile);
	}
	
	protected function needToBeRefreshed()
	{
		if (!$this->fs->exists($this->cacheFile)) {
			$this->fs->touch($this->cacheFile);
			return true;
		}
		
		$now = new DateTime();
		$myFileTime = new DateTime();
		$myFileTime::createFromFormat('U', filemtime($this->cacheFile));
		$myFileTime->modify('-10 minutes');
	
		if ($now < $myFileTime) {
			return true;
		}
		
		return false;
	}
	
	protected function refreshCache($path)
	{
		$response = $this->browser->get($path);
		$this->fs->dumpFile(
			$this->cacheFile,
			$response->getContent()
		);
	}
}