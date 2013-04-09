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
	);

	// URL / Username for Twitter accounts
	protected $twitter_streams = array(
	);

	// Number of posts to limit the output by
	protected static $limit = 3;

	// Instance variable where we keep the data
	protected $data = array();

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

		foreach($this->twitter_streams as $arr)
		{
			$this->FetchTweets($arr);
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
			fwrite($handle, $html);
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
	 */
	protected function FetchTweets($arr)
	{
		list($pageURL, $username) = $arr;

		$json = file_get_contents($pageURL);

		if(empty($json))
			return;

		$decoded = json_decode($json);

		$i = 0;
		foreach($decoded->results as $result)
		{
			$this->data[strtotime($result->created_at)] = array(
				'source'  => 'twitter',
				'content' => $result->text,
				'link'    => "http://twitter.com/$username/status/{$result->id}",
				'created' => $result->created_at
			);

			if(++$i >= 5) break;
		}
	}
}