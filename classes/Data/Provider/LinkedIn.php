<?php
namespace Claromentis\Social\Data\Provider;

use Claromentis\Social\Data\Provider as DataProvider;
use Claromentis\Social\Configuration\Provider as ConfigProvider;
use Claromentis\Social\Data\Person;
use Claromentis\Social\Data\Post\LinkedIn as LinkedInPost;

use LinkedIn\LinkedIn;

/**
 * Concrete implementation of a data provider
 *
 * @author Nathan Crause <nathan at crause.name>
 */
class LinkedIn extends DataProvider {
	
	private $api;
	
	public function __construct(\Claromentis\Social\Configuration\Provider $configuration) {
		parent::__construct($configuration);
		
		$this->api = new LinkedIn([
			'api_key'		=> $configuration->getApiKey(),
			'api_secret'	=> $configuration->getSecretKey(),
			'callback_url'	=> 'http://chicken.coop'
		]);
	}
	
	public function getPerson($id = NULL) {
		throw new Exception('Not implemented');
	}

	public function getStream() {
		
	}

}
