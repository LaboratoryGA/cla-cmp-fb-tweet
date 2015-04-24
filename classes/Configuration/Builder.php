<?php
namespace Claromentis\Social\Configuration;

/**
 * Defines a single method required by all builders
 *
 * @author Nathan Crause <nathan at crause.name>
 */
interface Builder {
	
	/**
	 * Constructs a new configuration instance with all configured providers
	 * and streams
	 */
	public function build();
	
}
