<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class ChannelFeed {

	function __construct($username, $page = 1)
	{
		// $gallery = 'uploads', 'favorites', etc...
		$gallery = 'uploads';
		$this->username=$username;
		$this->feedUrl=$url='http://gdata.youtube.com/feeds/api/users/'.$username.'/'.$gallery.'?start-index='.$page.'&max-results=10';
		//$this->feedUrl=$url='http://gdata.youtube.com/feeds/api/users/'.$username;
		$this->feed=simplexml_load_file($url);
	}

	//public function getYTid() {
	public function getYTid($ytURL = null) {

		if(empty($ytURL)) {
			$ytURL = $this->feed->entry->link['href'];
		}

		$ytvIDlen = 11; // This is the length of YouTube's video IDs

		// The ID string starts after "v=", which is usually right after 
		// "youtube.com/watch?" in the URL
		$idStarts = strpos($ytURL, "?v=");

		// In case the "v=" is NOT right after the "?" (not likely, but I like to keep my 
		// bases covered), it will be after an "&":
		if($idStarts === FALSE)
			$idStarts = strpos($ytURL, "&v=");
		// If still FALSE, URL doesn't have a vid ID
		if($idStarts === FALSE)
			die("YouTube video ID not found. Please double-check your URL.");

		// Offset the start location to match the beginning of the ID string
		$idStarts +=3;

		// Get the ID string and return it
		$ytvID = substr($ytURL, $idStarts, $ytvIDlen);    
		return $ytvID;   
	}
	public function showFullFeed()
	{ 
		foreach($this->feed->entry as $video){
			$vidarray[] = $video->link['href'];
		}
		return $vidarray;
	}
};
$youtube = new ChannelFeed('IndalLighting');
//$youtube = new ChannelFeed('guiwargo');
$vids = $youtube->showFullFeed();
//$vidIDs = array_map($youtube->getYTid(),$vids);

function debug($array) {
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}

foreach($vids as $vid) {
	$id = $youtube->getYTid((string)$vid);
	echo '
	<object style="height: 390px; width: 640px"><param name="movie" value="http://www.youtube.com/v/' . $id . '?version=3"><param name="allowFullScreen" value="true"><param name="allowScriptAccess" value="always"><embed src="http://www.youtube.com/v/' . $id . '?version=3" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="640" height="360"></object>
	';
}
