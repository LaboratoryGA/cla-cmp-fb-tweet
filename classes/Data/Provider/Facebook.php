<?php
namespace Claromentis\Social\Data\Provider;

use Claromentis\Social\Data\Provider as DataProvider;
use Claromentis\Social\Configuration\Provider as ConfigProvider;
use Claromentis\Social\Data\Person;
use Claromentis\Social\Data\Post\Facebook as FacebookPost;

use Facebook\Entities\AccessToken;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;

/**
 * Concrete implementation of a data provider
 *
 * @author Nathan Crause
 * @property \Claromentis\Social\Configuration\Provider\Facebook $configuration the facebook configuration instance
 */
class Facebook extends DataProvider {
	
	private $session;
	
	public function __construct(ConfigProvider $configuration) {
		parent::__construct($configuration);
		
//		$this->session = FacebookSession::newAppSession(
//				$configuration->getAppID(), $configuration->getAppSecret());
		// although the above would SEEM more "correct", Facebook's SDK is
		// poorly written enough that it actually relies pretty heavily on
		// "default"s
		FacebookSession::setDefaultApplication($configuration->getAppID(), 
				$configuration->getAppSecret());
				
		// make sure the access token is extended
//		$this->session = (new FacebookSession($configuration->getAccessToken()))
//			->getLongLivedSession();

//		$this->session = FacebookSession::newAppSession();
		
//		$appSession = new FacebookSession($configuration->getAccessToken());
//		$token = $appSession->getAccessToken()->extend();
//		$this->session = new FacebookSession($token);
		
		$this->session = new FacebookSession($this->getAppToken($configuration));
	}
	
	private function getAppToken(ConfigProvider $configuration) {
		$session = FacebookSession::newAppSession();
		$request = new FacebookRequest($session, 'GET', '/oauth/access_token', [
			'grant_type'	=> 'client_credentials',
			'client_id'		=> $configuration->getAppID(),
			'client_secret'	=> $configuration->getAppSecret()
		]);
		$response = $request->execute();
		
		return new AccessToken($response->getResponse()['access_token']);
	}
	
	private $appSecretProof;
	
	public function getAppSecretProof() {
		return $this->appSecretProof = $this->appSecretProof ?:
				hash_hmac('sha256', $this->session->getAccessToken(),
						$this->configuration->getAppSecret());
	}
	
	public function getPerson($id = NULL) {
		$id = $id ?: 'me';
		
		$request = new FacebookRequest($this->session, 'GET', "/{$id}", [
			'appsecret_proof' => $this->getAppSecretProof()
		]);
		$response = $request->execute();
		/* @var $graphObject \Facebook\GraphUser */
		$graphObject = $response->getGraphObject('Facebook\GraphUser');
		
		return new Person($graphObject->getName(), 
				$graphObject->getLink(), $graphObject);
//		return $graphObject;
	}

	public function getStream() {
		$request = new FacebookRequest($this->session, 'GET', "/{$this->configuration->getResource()}/feed", [
			'appsecret_proof' => $this->getAppSecretProof()
		]);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		
		$posts = [];
		
		foreach ($graphObject->getPropertyAsArray('data') as $post) {
			$posts[] = new FacebookPost(
					date_create($post->getProperty('created_time')),
					$post
					);
			
//			die('***<pre>' . htmlentities(end($posts)->getTitleHTML()) . "\r\n" 
//					. htmlentities(end($posts)->getBodyHTML()) . '</pre>***');
		}
		
//		die('<pre>Posts: ' . print_r($posts, true) . '</pre>');
		
		return $posts;
	}

}
