<?php
namespace Claromentis\Social\Configuration\Provider;

use Claromentis\Social\Configuration\Provider;

/**
 * Configuration provider for integrating with YouTube
 *
 * @author Nathan Crause
 */
class YouTube implements Provider {

	public function getName() {
		return 'YouTube';
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
