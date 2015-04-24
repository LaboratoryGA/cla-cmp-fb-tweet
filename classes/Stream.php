<?php
namespace Claromentis\Social;

/**
 * Retrieves posts for a specific provider and instance.
 *
 * @author Nathan Crause
 */
class Stream {
	
	/**
	 * The number of minutes which records should be kept alive
	 */
	const TTL = 15;
	
	/**
	 * Loops through all the instances, feeding all of them in
	 * 
	 * @global Configuration $cfg_social
	 */
	public static function all() {
		global $cfg_social;
		
		foreach ($cfg_social->getActiveProviders() as $provider) {
			foreach ($cfg_social->getProviderInstances($provider) as $name) {
				$feed = new self($provider, $name);
				
				$feed->run();
			}
		}
	}
	
	private $instance;
	
	private $info;
	
	public function __construct($provider, $name) {
		global $cfg_social;
		
		$this->info = [
			'provider'	=> $provider,
			'name'		=> $name
		];
		$this->instance = $cfg_social->getProviderInstance($provider, $name);
	}
	
	public function run() {
		$cache = new Cache($this->info['provider'] , $this->info['name']);
		$feed = $this->instance->connect()->getStream();
		
//		echo '<pre>To store: ' . print_r($feed, true) . '</pre>';
		
		$cache->set($feed);
//		die('<pre>Stored: ' . print_r($cache->get(), true) . '</pre>');
	}
	
}
