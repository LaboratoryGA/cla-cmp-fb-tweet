<?php
namespace Claromentis\Social\Data\Provider;

use Claromentis\Social\Data\Provider as DataProvider;
use Claromentis\Social\Configuration\Provider as ConfigProvider;
use Claromentis\Social\Data\Person;
use Claromentis\Social\Data\Post\Twitter as TwitterPost;

use TwitterOAuth\Auth\ApplicationOnlyAuth;
use TwitterOAuth\Serializer\ArraySerializer;

/**
 * Concrete implementation of a data provider
 *
 * @author Nathan Crause
 */
class Twitter extends DataProvider {
	
	const MAX_POSTS = 20;
	
	/**
	 *
	 * @var \TwitterOAuth\Auth\ApplicationOnlyAuth 
	 */
	private $app;
	
	public function __construct(ConfigProvider $configuration) {
		parent::__construct($configuration);
		
		$this->app = new ApplicationOnlyAuth([
			'consumer_key'		=> $configuration->getConsumerKey(),
			'consumer_secret'	=> $configuration->getConsumerSecret(),
		], new ArraySerializer());
	}

	
	public function getPerson($id = NULL) {
		throw new Exception('Not implemented');
	}

	public function getStream() {
		$response = $this->app->get('statuses/user_timeline', [
			'screen_name' => $this->configuration->getScreenName(),
			'count' => self::MAX_POSTS,
			'exclude_replies' => true
		]);
		
		$posts = [];
		
		foreach ($response as $tweet) {
			$posts[] = new TwitterPost(date_create($tweet['created_at']), $tweet);
		}
		
		return $posts;
	}

}
