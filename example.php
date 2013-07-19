<?php

require_once('Creare_Twitter.php');

$twitter = new Creare_Twitter();

##########################################################
# You can either edit the following per implementation - #
# or simply edit them in the class and remove all of     #
# these property overrides                               #
##########################################################

# username and number of tweets you want to return

$twitter->screen_name = "creareseo";
$twitter->not = 1;

# twitter credentials - check https://www.creare.co.uk/whatever-happened-to-twitters-latest-tweets for a how-to

$twitter->consumerkey = "XXXX";
$twitter->consumersecret = "XXXX";
$twitter->accesstoken = "XXXX";
$twitter->accesstokensecret = "XXXX";

# cache file
	
$twitter->cachefile = $_SERVER['DOCUMENT_ROOT']."/twitter/twitter.txt";

/* You can optionally change the following public properties

$twitter->tags = true; // show all html tags? FALSE = remove all tags
$twitter->nofollow = true; // all links to be no-follow?
$twitter->newwindow = true; // all links to open in new window?
$twitter->hashtags = true; // link up hashtags to twitter search?
$twitter->attags = true; // link up @ tags to profile pages

*/

$tweets = $twitter->getLatestTweets();

foreach($tweets as $tweet){
	echo "<p>".$tweet['tweet']." - ".$tweet['time']."</p>";	
}