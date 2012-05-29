<?php
/**
*    Convert tweets from English to Piratespeak 
*    Copyright (C) 2009 Anirudh Surendranath
*
*    This program is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with this program; if not, write to the Free Software
*    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*
**/

function theme_tweets($tweets,$username) {
  $output .= '<div class="tweets">';
  $counter = 1;
  foreach($tweets as $tweet) {
    if(stripos($tweet,$username.': ')===0) {
      $tweet = substr($tweet,strlen($username.': '));
    }
    $odd_even_class = ($counter%2) ? 'tweet-odd' : 'tweet-even';
    $output .= '  <p class="tweet '.$odd_even_class.'" >'.$tweet.'</p>';
    $counter++;
  }
  $output .= '</div>';
  return $output;
}

// SELECT * FROM piratespeak.translate WHERE html IN (SELECT text from url = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=gingerjoos&count=10&include_rts=true")
//
//
function get_pirate_tweets($username) {
	$tweet_texts = fetch_tweets($username);
	if(!$tweet_texts) {
		return FALSE;
	}
	$themed_content = theme_tweets($tweet_texts,$username);
	return translate_to_piratespeak($themed_content);
}

function fetch_url($url,$params,$method='GET') {
	if($method == 'GET')
		$url = $url . '?' . http_build_query($params);
  $ch = curl_init();
  curl_setopt($ch,CURLOPT_TIMEOUT,YQL_FETCH_TIMEOUT);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_FAILONERROR,FALSE);
  curl_setopt($ch,CURLOPT_URL,$url);
  $result = curl_exec($ch);
  $status_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
	curl_close($ch);
  return array(
    'status_code' => $status_code,
    'result'      => $result,
  );
}

function translate_to_piratespeak($content) {
	$url = build_yql_url($content);
	$api_url = $url['api_url'];
	$params = $url['params'];
	$response = fetch_url($api_url,$params);
	if($response['status_code'] != '200')
		return FALSE;
	var_dump($response['result']);exit;
	return $response['result'];
}

function fetch_tweets($username) {
	$api_url = 'https://api.twitter.com/1/statuses/user_timeline.json';
	$params = array(
		'screen_name' => $username,
		'include_rts' => 'true',
	);
	$response = fetch_url($api_url,$params);
	if($response['status_code'] != '200')
		return FALSE;
	$tweets = json_decode($response['result']);
	$tweet_text = array();
	foreach($tweets as $tweet) {
		$tweet_text[] = $tweet->text;
	}
	return $tweet_text;
}

function build_yql_url($content) {
  $api_url = 'http://query.yahooapis.com/v1/public/yql?';
	$query = build_query($content);

  $params = array (
    'q'      => $query,
    'format' => 'json',
    'env'    => 'store://kid666.com/piratespeak',
	);
	return compact('api_url','params');
}

function build_query($content) {
  $query = 'SELECT * FROM piratespeak.translate WHERE html = "' . htmlentities($content) . '"';
  return $query;
}

function get_default_tweetname() {
  $username = strip_tags($_GET['tweetname']);
  if(!empty($username)) {
    return $username;
  }
  else if(defined('DEFAULT_TWEETNAME')) {
    return DEFAULT_TWEETNAME;
  }
  else {
    return 'Yer twitter username';
  }
}

// vim: tabstop=2 softtabstop=2 shiftwidth=2 expandtab ai
