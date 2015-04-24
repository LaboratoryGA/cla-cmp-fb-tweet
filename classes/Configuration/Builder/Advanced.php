<?php
namespace Claromentis\Social\Configuration\Builder;

use Claromentis\Social\Configuration;
use Claromentis\Social\Configuration\Builder;

/**
 * Description of Advanced
 *
 * @author Nathan Crause <nathan at crause.name>
 */
class Advanced implements Builder {
	
	public function build() {
		global $cfg_social_streams;
		
		$config = new Configuration();
		
		foreach ($cfg_social_streams as $stream) {
			$instance = $config->addProviderInstance($stream['provider'],
					key_exists('name', $stream)
							? $stream['name']
							: 'default');
			
			// remove provider and name from arguments
			unset($stream['provider']);
			unset($stream['name']);
			
			// use the remainder of the name to set attributes
			foreach ($stream as $arg => $val) {
				$method = 'set' . ucfirst($arg);
				
				call_user_func(array($instance, $method), $val);
			}
		}
		
		return $config;
	}

}
