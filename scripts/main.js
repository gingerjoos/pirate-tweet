$(document).ready(function() {
	$('#pt-form').submit(function () {
		var username = $('#id-username').val();
		var tweetContainer = $('#tweet-wrap');
		tweetContainer.show();
		tweetContainer.addClass('loading');
		fetch_tweets_and_translate(username,tweetContainer);
		return false;
	});
});

function fetch_tweets_and_translate(username,tweetContainer) {
	var url = 'https://api.twitter.com/1/statuses/user_timeline.json?screen_name=' + username + '&include_rts=true&callback=?';
	$.getJSON(url,function(data) {
		var out = '<div class="t-msg">Translating...</div>' + '<ol>';
		$.each(data,function(i,item) {
			out = out + '<li>' + item.text + '</li>';
		});
		out = out + '</ol>';
		tweetContainer.html(out);
		translate_to_ps(out,tweetContainer);
	});
}

function translate_to_ps(tweets,tweetContainer) {
	var api_url = 'http://query.yahooapis.com/v1/public/yql?callback=?';
	var query = 'SELECT * FROM piratespeak.translate WHERE html = "' + encodeURIComponent(tweets) + '"';
	$.getJSON(api_url,
			{
			'format':'json',
			'env':'store://kid666.com/piratespeak',
			'q':query,
			},
			function(data) {
				var result = data.query.results.result;
				tweetContainer.html(result);
				tweetContainer.removeClass('loading');
				$('.t-msg').hide();
			});
}
