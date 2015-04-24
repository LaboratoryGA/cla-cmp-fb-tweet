<?php
namespace Claromentis\Social\Configuration\Provider;

use Claromentis\Social\Configuration\Provider;

/**
 * Configuration provider for integrating with LinkedIn
 *
 * @author Nathan Crause
 */
class LinkedIn implements Provider {

	public function getName() {
		return 'LinkedIn';
	}

	public function getProtocol() {
		return 'OAuth';
	}

	public function getApiVersion() {
		return '?';
	}
	
	public function connect() {
		throw new \Exception('Not yet implemented');
	}
	
	private $apiKey;
	
	public function getApiKey() {
		return $this->apiKey;
	}

	public function setApiKey($apiKey) {
		$this->apiKey = $apiKey;
		return $this;
	}

	private $secretKey;
	
	public function getSecretKey() {
		return $this->secretKey;
	}

	public function setSecretKey($secretKey) {
		$this->secretKey = $secretKey;
		return $this;
	}

	private $userToken;
	
	public function getUserToken() {
		return $this->userToken;
	}

	public function setUserToken($userToken) {
		$this->userToken = $userToken;
		return $this;
	}

	private $userSecret;
	
	public function getUserSecret() {
		return $this->userSecret;
	}

	public function setUserSecret($userSecret) {
		$this->userSecret = $userSecret;
		return $this;
	}
	
}
