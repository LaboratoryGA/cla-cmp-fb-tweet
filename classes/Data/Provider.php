<?php
namespace Claromentis\Social\Data;

use Claromentis\Social\Configuration\Provider as ConfigProvider;

/**
 * This class defines the basic structure required
 *
 * @author Nathan Crause
 */
abstract class Provider {
	
	/**
	 *
	 * @var ConfigProvider
	 */
	protected $configuration;
	
	public function __construct(ConfigProvider $configuration) {
		$this->configuration = $configuration;
	}
	
	/**
	 * Retrieves the person whose account is being accessed.
	 * 
	 * @return Person
	 */
	public abstract function getPerson($id = NULL);
	
	/**
	 * Retrieves a stream of data from the social media source.
	 * 
	 * @return array[Post]
	 */
	public abstract function getStream();
	
}
