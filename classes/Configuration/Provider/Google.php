<?php
namespace Claromentis\Social\Configuration\Provider;

use Claromentis\Social\Configuration\Provider;

/**
 * Configuration provider for integrating with Google+
 *
 * @author Nathan Crause
 */
class Google implements Provider {

	public function getName() {
		return 'Google+';
	}

	public function getProtocol() {
		return 'OAuth';
	}

	public function getApiVersion() {
		return '1.1.3';
	}
	
	public function connect() {
		throw new \Exception('Not yet implemented');
	}
}
