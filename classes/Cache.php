<?php
namespace Claromentis\Social;

use ClaCache;

/**
 * This class handles the details of caching
 *
 * @author Nathan Crause
 */
class Cache {
	
	private $key;
	
	private $provider;
	
	private $name;
	
	/**
	 * Creates a new streamfeed cache instance specifically for the supplied
	 * provider and instance name
	 * 
	 * @param string $provider the name ofhte provider (i.e. facebook, twitter)
	 * @param string $name the instance name
	 */
	public function __construct($provider, $name) {
		$this->provider = $provider;
		$this->name = $name;
		$this->key = "social::{$provider}:{$name}";
	}
	
	/**
	 * Utility method which wipes out all social cache records
	 */
	public static function clear() {
		ClaCache::ClearGlobal('social');
	}
	
	/**
	 * Tests if the cache contains a record for the current provider and
	 * instance
	 * 
	 * @return boolean <code>true</code> if records exist, <code>false</code>
	 * otherwise
	 */
	public function exists() {
		return !!$this->get();
	}
	
	/**
	 * Retrieves the records for the current provider and instance
	 * 
	 * @return mixed the data, or <code>NULL</code> if not records exist
	 */
	public function get() {
		return ClaCache::GetGlobal($this->key);
	}
	
	/**
	 * Stores records for the current provider and instance
	 * 
	 * @param mixed $data the data to store
	 */
	public function set($data) {
//		die(serialize($data));
		// store records for twice as long as the TTL is expected - this is to
		// cater for potential latency during processing
		ClaCache::SetGlobal($this->key, $data, (Stream::TTL * 2) * 60);
	}
	
	/**
	 * Deletes any records associated with the current provider and instance
	 */
	public function delete() {
		ClaCache::DeleteGlobal($this->key);
	}

}
