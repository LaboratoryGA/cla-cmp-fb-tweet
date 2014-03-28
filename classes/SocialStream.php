<?php

require_once dirname(__FILE__)."/base_facebook.php";
require_once dirname(__FILE__)."/facebook.php";

/**
 * !! Requirements:
 * These four variables should be present in config.php
 *
 * $cfg_facebook_app_id;
 * $cfg_facebook_app_sercet;
 * $cfg_twitter_consumer_key;
 * $cfg_twitter_consumer_secret;
 *
 * $cfg_social_stream_facebook_pages = array();
 * $cfg_social_stream_twitter_streams = array();
 *
 *
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
	);

	// Username for Twitter accounts
	protected $twitter_streams = array(
	);

	// Number of posts to limit how many are fetched  
	public static $limit = 3;

	// Instance variable where we keep the data
	protected $data = array();

	/**
	 * Start the process to do the import
	 *
	 * This method should be called by background_custom
	 */
	public function Go()
	{
		global $cfg_social_stream_facebook_pages;
		global $cfg_social_stream_twitter_streams;

		$this->facebook_pages = $cfg_social_stream_facebook_pages;
		$this->twitter_streams = $cfg_social_stream_twitter_streams;

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
		global $APPDATA;

		if(is_array($this->data))
		{
			foreach($this->data as $post)
			{
				if (isset($post['image_source']))
				{
					$avatar_visible = true;
					$avatar_source = $post['image_source'];
				}
				else
				{
					$avatar_visible = false;
					$avatar_source = '';
				}

				$args['posts.datasrc'][] = array(
					'post.+class'            => $post['source'],
					'post_content.body_html' => ClaText::ProcessPlain($post['content']),
					'post_link.href'         => $post['link'],
					'post_on.body'			 => $post['source'],
					'post_user_img.src'       => $avatar_source,
					'post_user_img.visible'   => $avatar_visible
				);
			}

			require_once('../common/templater.php');
			$html = json_encode($args); 

			$handle = fopen("{$APPDATA}/people/social_component.json", "w+");
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

		global $cfg_facebook_app_id;
		global $cfg_facebook_app_secret;

		if (empty($cfg_facebook_app_id) || empty($cfg_facebook_app_id))
			return null;

		$facebook = new Facebook(array(
			'appId' => $cfg_facebook_app_id,
			'secret' => $cfg_facebook_app_secret 
		));

		$facebook->setAccessToken($facebook->getAccessToken());
		$posts = $facebook->api($pageURL."/posts", array(
			'fields' => array('id', 'created_time', 'message')
		));
		$posts = $posts['data'];

		if(!is_array($posts) || empty($posts))
			return;

		$i = 0;

		$page_image = $facebook->api($pageURL."/picture", array('redirect'=>false)); 
		$page_image = $page_image['data']['url'];

		$count = 0;
		foreach($posts as $post)
		{
			if (isset($post['message'])) // only pull down statuses (as /posts response can return other info)
			{
				$linkParts = explode("_", $post['id']);
				$this->data[strtotime($post['created_time'])] = array(
					'source'  =>		'fb',
					'content' => 		$post['message'] == ' ' ? 'Untitled post' : $post['message'],
					'link'    => 		"http://facebook.com/".$linkParts[0]."/posts/".$linkParts[1],
					'created' => 		$post['created_time'],
					'image_source' =>		$page_image,
				);

				if(++$count >= self::$limit) break;
			}
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

		$count = 0;
		foreach($json as $result)
		{
			$this->data[strtotime($result['created_at'])] = array(
				'source'  => 'Twitter',
				'content' => $result['text'],
				'link'    => "http://twitter.com/{$result['user']['screen_name']}/status/{$result['id']}",
				'created' => $result['created_at'],
				'image_source' => $result['user']['profile_image_url']
			);

			if(++$count >= self::$limit) break;
		}
	}

	/**
	 * Get Twitter Access TOken
	 * @source http://www.thepicketts.org/2013/05/how-to-implement-application-only-authentication-of-twitter-api-v1-1-in-phpwordpress/
	 */
	protected function GetTwitterAccessToken()
	{
		global $cfg_twitter_consumer_key;
		global $cfg_twitter_consumer_secret;

		$encoded_consumer_key    = urlencode($cfg_twitter_consumer_key);
		$encoded_consumer_secret = urlencode($cfg_twitter_consumer_secret);

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

