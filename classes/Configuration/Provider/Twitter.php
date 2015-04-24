<?php
namespace Claromentis\Social\Configuration\Provider;

use Claromentis\Social\Configuration\Provider;

/**
 * Configuration provider for integrating with Twitter
 *
 * @author Nathan Crause
 */
class Twitter implements Provider {

	public function getName() {
		return 'Twitter';
	}

	public function getProtocol() {
		return 'OAuth';
	}

	public function getApiVersion() {
		return '1.1';
	}
	
	public function connect() {
		return new \Claromentis\Social\Data\Provider\Twitter($this);
	}
	
	private $consumerKey;
	
	public function getConsumerKey() {
		return $this->consumerKey;
	}

	public function setConsumerKey($consumerKey) {
		$this->consumerKey = $consumerKey;
		return $this;
	}

	private $consumerSecret;
	
	public function getConsumerSecret() {
		return $this->consumerSecret;
	}

	public function setConsumerSecret($consumerSecret) {
		$this->consumerSecret = $consumerSecret;
		return $this;
	}
	
	private $screenName;
	
	public function getScreenName() {
		return $this->screenName;
	}

	public function setScreenName($screenName) {
		$this->screenName = $screenName;
		return $this;
	}

}
