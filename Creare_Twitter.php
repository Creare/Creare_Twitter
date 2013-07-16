<?php

class Creare_Twitter
{
	
	public $screen_name = "crearegroup";
	public $not = 1;
	public $cachefile = "twitter.txt";
	public $consumerkey = "XXXX";
	public $consumersecret = "XXXX";
	public $accesstoken = "XXXX";
	public $accesstokensecret = "XXXX";
	
	public $tags = true;
	public $nofollow = true;
	public $newwindow = true;
	public $hashtags = true;
	public $attags = true;
	
	private function cleanTwitterName($twitterid)
	{
		$test = substr($twitterid,0,1);
		
		if($test == "@"){
			$twitterid = substr($twitterid,1);	
		}
		
		return $twitterid;
		
	}
	
	private function changeLink($string)
	{
		if(!$this->tags){
			$string = strip_tags($string);
		}
		if($this->nofollow){
			$string = str_replace('<a ','<a rel="nofollow"', $string);	
		}
		if($this->newwindow){
			$string = str_replace('<a ','<a target="_blank"', $string);	
		}
  		return $string;
 	}
	
	private function getTimeAgo($time)
	{
		   	$tweettime = strtotime($time); // This is the value of the time difference - UK + 1 hours (3600 seconds)
		   	$nowtime = time();
		   	$timeago = ($nowtime-$tweettime);
		   	$thehours = floor($timeago/3600);
		   	$theminutes = floor($timeago/60);
		   	$thedays = floor($timeago/86400);
  			/********************* Checking the times and returning correct value */
		   	if($theminutes < 60){
				if($theminutes < 1){
					$timemessage =  "Less than 1 minute ago";
				} else if($theminutes == 1) {
				 	$timemessage = $theminutes." minute ago";
				} else {
				 	$timemessage = $theminutes." minutes ago";
				}
			} else if($theminutes > 60 && $thedays < 1){
				 if($thehours == 1){
				 	$timemessage = $thehours." hour ago";
				 } else {
				 	$timemessage = $thehours." hours ago";
				 }
			} else {
				 if($thedays == 1){
				 	$timemessage = $thedays." day ago";
				 } else {
				 	$timemessage = $thedays." days ago";
				 }
			}
		return $timemessage;	
	}
	
	private function removeSpamCharacters($string)
	{
		$string = preg_replace('/[^(\x20-\x7F)]*/','', $string);
		return $string;
	}
	
	public function getTweets($tweets)
	{
		$t = array();
		$i = 0;
		foreach($tweets as $tweet)
		{	
			if(isset($tweet->retweeted_status)){
				$text = $this->removeSpamCharacters($tweet->retweeted_status->text);
			} else {
				$text = $this->removeSpamCharacters($tweet->text);
			}
			$urls = $tweet->entities->urls;
			$mentions = $tweet->entities->user_mentions;
			$hashtags = $tweet->entities->hashtags;
			if($urls){
				foreach($urls as $url){
					if(strpos($text,$url->url) !== false){
						$text = str_replace($url->url,'<a href="'.$url->url.'">'.$url->url.'</a>',$text);	
					}
				}
			}
			if($mentions && $this->attags){
				foreach($mentions as $mention){
					if(strpos($text,$mention->screen_name) !== false){
						$text = str_replace("@".$mention->screen_name." ",'<a href="http://twitter.com/'.$mention->screen_name.'">@'.$mention->screen_name.'</a> ',$text);	
					}
				}
			}
			if($hashtags && $this->hashtags){
				foreach($hashtags as $hashtag){
					if(strpos($text,$hashtag->text) !== false){
						$text = str_replace('#'.$hashtag->text." ",'<a href="http://twitter.com/search?q=%23'.$hashtag->text.'">#'.$hashtag->text.'</a> ',$text);	
					}
				}
			}
			$t[$i]["tweet"] = trim($this->changeLink($text));	
			$t[$i]["time"] = trim($this->getTimeAgo($tweet->created_at));
			$i++;
		}
		
		$this->saveCachedTweets($t);
		return $t;
	}
	
	private function saveCachedTweets($data)
	{
		$data = json_encode($data);
		$f = file_put_contents($this->cachefile, $data);
	}
	
	private function getCachedTweets()
	{
		return file_get_contents($this->cachefile);	
	}

 	public function getLatestTweets()
	{
		require_once('twitteroauth/twitteroauth.php');
		
		$twitterconn = new TwitterOAuth($this->consumerkey, $this->consumersecret, $this->accesstoken, $this->accesstokensecret);
 
		$latesttweets = $twitterconn->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$this->screen_name."&count=".$this->not);		
		
		if(!isset($latesttweets->errors)){
			return $this->getTweets($latesttweets);
		} else {
			return json_decode($this->getCachedTweets(), true);
		}
  		
	}
}