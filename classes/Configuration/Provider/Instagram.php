<?php
namespace Claromentis\Social\Configuration\Provider;

use Claromentis\Social\Configuration\Provider;

/**
 * Configuration provider for integrating with Instagram
 *
 * @author Nathan Crause
 */
class Instagram implements Provider {

	public function getName() {
		return 'Instagram';
	}

	public function getProtocol() {
		return 'OAuth';
	}

	public function getApiVersion() {
		return '1';
	}
	
	public function connect() {
		throw new \Exception('Not yet implemented');
	}
}
