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

function parse_tweets($results,$username) {
  $sxml = simplexml_load_string($results);
  $result = $sxml->results->result;
  $tweets = array();
  foreach($result as $tweet) {
    $tweets[] = $tweet[0];
  }
  return $tweets;
}

function fetch_yql_results($username) {
  $url = build_result_url($username);
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

function build_result_url($username) {
  $api_url = 'http://query.yahooapis.com/v1/public/yql?';
  $query = build_query($username);
  $params = array (
    'q'      => $query,
    'format' => 'xml',
    'env'    => 'store://kid666.com/piratespeak',
  );
  $query = $api_url . http_build_query($params);
  return $query;
}

function build_query($username) {
  $query = 'SELECT * FROM piratespeak.translate WHERE html IN (SELECT description FROM rss WHERE url = "' . get_rss_url($username) . '")';
  return $query;
}

function get_rss_url($username) {
  return 'http://twitter.com/statuses/user_timeline/'.$username.'.rss';
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
