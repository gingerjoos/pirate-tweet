<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
  <title>Tweets in Pirate Speak</title>
  <link rel="stylesheet" href="pirate-tweet.css" type="text/css">
</head>
<body>

<?php
  require_once('common.php');
  define(DEFAULT_TWEETNAME,'gjoos');
?>
<h2>Pirate tweet</h2>
<form action="index.php" method="GET">
  <textarea rows="1" name="tweetname" id="text" ><?php echo get_default_tweetname() ?></textarea>
  <input type="submit" value="Fetch me tweets, hearty">
</form>

<?php
  if(!empty($_GET['tweetname'])) {
    $username = $_GET['tweetname'];
  }
  else {
    $username = DEFAULT_TWEETNAME;
  }
  $username = strip_tags($username);
  $yql_results = fetch_yql_results($username);
  if($yql_results['status_code'] != 200) {
    echo $yql_results['status_code'].' : There be some error!';
  }
  else {
    $tweets = parse_tweets($yql_results['result'],$username);
    echo theme_tweets($tweets,$username);
  }
?>

</body>
</html>
