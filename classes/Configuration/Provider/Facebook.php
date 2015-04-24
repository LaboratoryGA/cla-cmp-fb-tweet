<?php
namespace Claromentis\Social\Configuration\Provider;

use Claromentis\Social\Configuration\Provider;

/**
 * Configuration provider for integrating with Facebook
 *
 * @author Nathan Crause
 */
class Facebook implements Provider {

	public function getName() {
		return 'Facebook';
	}

	public function getProtocol() {
		return 'OAuth';
	}
	
	public function getApiVersion() {
		return '2.2';
	}

	public function connect() {
		return new \Claromentis\Social\Data\Provider\Facebook($this);
	}
	
	protected $appID;
	
	public function getAppID() {
		return $this->appID;
	}

	/**
	 * 
	 * @param type $appID
	 * @return \Claromentis\Social\Configuration\Provider\Facebook
	 */
	public function setAppID($appID) {
		$this->appID = $appID;
		return $this;
	}

	protected $appSecret;
	
	public function getAppSecret() {
		return $this->appSecret;
	}

	/**
	 * 
	 * @param type $appSecret
	 * @return \Claromentis\Social\Configuration\Provider\Facebook
	 */
	public function setAppSecret($appSecret) {
		$this->appSecret = $appSecret;
		return $this;
	}
	
//	protected $accessToken;
//	
//	public function getAccessToken() {
//		return $this->accessToken;
//	}
//
//	/**
//	 * 
//	 * @param string $accessToken
//	 * @return \Claromentis\Social\Configuration\Provider\Facebook
//	 */
//	public function setAccessToken($accessToken) {
//		$this->accessToken = $accessToken;
//		return $this;
//	}

	protected $resource = 'Claromentis';
	
	public function getResource() {
		return $this->resource;
	}

	/**
	 * 
	 * @param string $resource
	 * @return \Claromentis\Social\Configuration\Provider\Facebook
	 */
	public function setResource($resource) {
		$this->resource = $resource;
		return $this;
	}

}
