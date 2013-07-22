<?php
/**
 * A class to pull through FB page posts and Tweets
 *
 * It will trawl through several public Facebook pages and Twitter acounts and amalgamate
 * them into one HTML template
 *
 * This class should only ever be called as a background process (it's super slow!)
 * To call from background_custom use the following:
 *
 * $social = new SocialStream();
 * $social->Go();
 */
class SocialStream {

	// URL's for Facebook pages
	protected $facebook_pages = array(
		// 'https://www.facebook.com/feeds/page.php?id=121829278435&format=json'
	);

	// URL / Username for Twitter accounts
	protected $twitter_streams = array(
		'firefighters999'
	);

	// Number of posts to limit the output by
	protected static $limit = 3;

	// Instance variable where we keep the data
	protected $data = array();

	// Twitter Consumer API key
	protected $consumer_key = 'LkHv1Jjqo0myrkA5CsJqAg';

	// Twitter Consumer Secret key
	protected $consumer_secret = 'Bi3bu0NW1llXvH7j462O0n3vy5M0TFvNb1NMuWmurZc';

	/**
	 * Start the process to do the import
	 *
	 * This method should be called by background_custom
	 */
	public function Go()
	{
		foreach($this->facebook_pages as $pageURL)
		{
			$this->FetchFacebookEntries($pageURL);
		}

		foreach($this->twitter_streams as $account)
		{
			$this->FetchTweets($account);
		}

		krsort($this->data);

		$this->FlushToFile();
	}

	/**
	 * Flush the output to a file
	 */
	protected function FlushToFile()
	{
		global $BASE_LOCATION;

		$this->TruncateData();

		if(is_array($this->data))
		{
			foreach($this->data as $post)
			{
				$args['posts.datasrc'][] = array(
					'post.+class'            => $post['source'],
					'post_content.body_html' => ClaText::ParseLinks($post['content']),
					'post_link.href'         => $post['link']
				);
			}

			require_once('../common/templater.php');
			$html = process_cla_template('social/template.html', $args);

			$handle = fopen("{$BASE_LOCATION}interface_default/social/output.html", "w+");
			fwrite($handle, iconv(mb_detect_encoding($html), 'UTF-8//IGNORE', $html));
			fclose($handle);
		}
	}

	/**
	 * Truncate the gathered data
	 */
	protected function TruncateData()
	{
		$this->data = array_slice($this->data, 0, self::$limit);
	}

	/**
	 * Fetch Facebook posts
	 */
	protected function FetchFacebookEntries($pageURL)
	{
		$curl = curl_init();
		// We pretend that we're Google Chrome, because it's awesome
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.79 Safari/537.4');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, $pageURL);
		$json = curl_exec($curl);
		$decoded = json_decode($json);

		if(!is_array($decoded->entries) || !count($decoded->entries))
			return;

		$i = 0;
		foreach($decoded->entries as $entry)
		{
			 $this->data[strtotime($entry->published)] = array(
				'source'  => 'fb',
				'content' => $entry->title == ' ' ? 'Untitled post' : $entry->title,
				'link'    => $entry->alternate,
				'created' => $entry->published
			);

			if(++$i >= 5) break;
		}
	}

	/**
	 * Fetch Tweets
	 * @source http://www.thepicketts.org/2013/05/how-to-implement-application-only-authentication-of-twitter-api-v1-1-in-phpwordpress/
	 */
	protected function FetchTweets($account)
	{

		$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
		$q = urlencode(trim($account));
		$formed_url ='?screen_name='.$q.'&count=5';

		$url .= $formed_url;

		$access_token = $this->GetTwitterAccessToken();

		$headers = array(
			"GET /1.1/search/tweets.json".$url." HTTP/1.1",
			"Host: api.twitter.com",
			"User-Agent: Claromentis Social App",
			"Authorization: Bearer " . $access_token,
			"Content-Type: application/x-www-form-urlencoded;charset=UTF-8",
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$header = curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

		$feed = curl_exec($ch);

		$feed = preg_replace('/\\\u([0-9a-z]{4})/', '&#x$1;', $feed);
		$json = json_decode($feed, true);


		curl_close($ch);

		if(isset($json['errors']) && count($json['errors'])) return;

		$i = 0;
		foreach($json as $result)
		{
			$this->data[strtotime($result['created_at'])] = array(
				'source'  => 'twitter',
				'content' => $result['text'],
				'link'    => "http://twitter.com/{$result['user']['screen_name']}/status/{$result['id']}",
				'created' => $result['created_at']
			);

			if(++$i >= 5) break;
		}
	}

	/**
	 * Get Twitter Access TOken
	 * @source http://www.thepicketts.org/2013/05/how-to-implement-application-only-authentication-of-twitter-api-v1-1-in-phpwordpress/
	 */
	protected function GetTwitterAccessToken()
	{
		$encoded_consumer_key    = urlencode($this->consumer_key);
		$encoded_consumer_secret = urlencode($this->consumer_secret);

		$bearer_token = $encoded_consumer_key . ':' . $encoded_consumer_secret;
		$base64_encoded_bearer_token = base64_encode($bearer_token);

		$url = "https://api.twitter.com/oauth2/token/";

		$headers = array(
			"POST /oauth2/token HTTP/1.1",
			"Host: api.twitter.com",
			"User-Agent: Claromentis Social App",
			"Authorization: Basic " . $base64_encoded_bearer_token,
			"Content-Type: application/x-www-form-urlencoded;charset=UTF-8",
			"Content-Length: 29"
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$header = curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);

		$json = json_decode($response, true);

		if (!isset($json["errors"]) && $json["token_type"] == 'bearer')
		{
			return $json["access_token"];
		}
		else
		{
			return null;
		}
	}
}