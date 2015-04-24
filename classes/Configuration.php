<?php
namespace Claromentis\Social;

/**
 * This class holds the configuration information for all the social media
 * feeds.
 *
 * @author Nathan Crause
 * @method \Claromentis\Social\Configuration\Provider\Facebook addFacebook(string $name) Adds a new named FaceBook instance
 * @method \Claromentis\Social\Configuration\Provider\Twitter addTwitter(string $name) Adds a new named Twitter instance
 * @method \Claromentis\Social\Configuration\Provider\LinkedIn addLinkedIn(string $name) Adds a new named LinkedIn instance
 */
class Configuration {
	
	/**
	 * Internal register of provide name and the associated class name
	 *
	 * @var array['provider'=>'ClassName'] 
	 */
	private static $mapping = [];
	
	public static function registerProvider($name, $class) {
		self::$mapping[$name] = $class;
	}
	
	/**
	 * Internal list of "aliases". This allows the legacy "source_filter" to
	 * be used for the component, but still reference the more modern layout.
	 * <p>
	 * The value of each element is itself an array - this allows for multiple
	 * instances to be referred in a single alias.
	 *
	 * @var array['alias'=>array[stdClass]] 
	 */
	private $aliases = [];
	
	public function addAlias($alias, $provider, $name) {
		$this->aliases[$alias] = $this->aliases[$alias] ?: [];
		
		$this->aliases[$alias][] = (object) compact('provider', 'name');
	}
	
	public function getAlias($alias) {
		return $this->aliases[$alias] ?: [];
	}
	
	public function aliasExists($alias) {
		return key_exists($alias, $this->aliases);
	}
	
	/**
	 * Internal register for providers, and all instance names within that
	 * provider
	 *
	 * @var array['providerName'=>array['instanceName'=>Provider]] 
	 */
	private $providerRegistries = [];
	
	/**
	 * Add a new provider instance
	 * 
	 * @param string $providerName the provider name
	 * @param string $instanceName the distinct instance name
	 * @return social\config\Provider
	 */
	public function addProviderInstance($providerName, $instanceName = 'default') {
		// make sure the registry entry exists
		$this->providerRegistries[$providerName] = 
				@$this->providerRegistries[$providerName] ?: array();
		// determine the class (by mapping) and create an instance
		$class = self::$mapping[$providerName];
		$object = new $class();
		// store the instance
		$this->providerRegistries[$providerName][$instanceName] = $object;
		
		return $object;
	}
	
	/**
	 * 
	 * @param string $provider
	 * @param string $name
	 * @return Configuration\Provider
	 */
	public function getProviderInstance($provider, $name) {
		$registry = ($this->providerRegistries[$provider] = 
				$this->providerRegistries[$provider] ?: array());
		
		return $registry[$name];
	}
	
	/**
	 * Retrieves a list of providers who actively have instances
	 * 
	 * @return array[string]
	 */
	public function getActiveProviders() {
		return array_keys($this->providerRegistries);
	}
	
	/**
	 * Retrieves a list of instances for a specified provider
	 * 
	 * @param string $provider the name of the provider whose instances we
	 * wish to retrieve
	 * @return array[string]
	 */
	public function getProviderInstances($provider) {
		return array_keys($this->providerRegistries[$provider]);
	}
	
	private $purgeOnLegacy;
	
	/**
	 * Checks if any previous configurations should be purged if legacy
	 * configuration options are detected.
	 * 
	 * @return boolean <code>true</code> if the existance of legacy should
	 * cause a configuration purge
	 */
	public function getPurgeOnLegacy() {
		return $this->purgeOnLegacy;
	}

	/**
	 * Toggles whether a configuration purge should occur if legacy
	 * configurations are detected.
	 * 
	 * @param boolean $purgeOnLegacy <code>true</code> if purge should occur
	 * @return \Claromentis\Social\Configuration
	 */
	public function setPurgeOnLegacy($purgeOnLegacy) {
		$this->purgeOnLegacy = $purgeOnLegacy;
		return $this;
	}
	
	/**
	 * Removes all configuration options, excluding provider name mappings.
	 */
	public function purge() {
		$this->aliases = [];
		$this->providerRegistries = [];
	}
	
	/**
	 * This method provides a shortcode to <code>addProvider()</code>. Invoking
	 * a method named <code>addFacebook</code> would cause an internal
	 * invocation of <code>addProvider('facebook')</code>
	 * 
	 * @param string $name the name of the method
	 * @param mixed $arguments
	 * @return social\config\Provider
	 */
	public function __call($name, $arguments) {
		// if the method doesn't start with "add", trigger an error
		if (!preg_match('/^add(.+)$/', $name, $matches)) {
			trigger_error("No such method: '$name'", E_USER_ERROR);
			return;
		}
		
		// extract the name of the provider
		$provider = strtolower($matches[1]);
		$args = $arguments ?: array();
		// push it into the argument array (at the beginning)
		array_unshift($args, $provider);
		
		return call_user_func_array(array($this, 'addProviderInstance'), $args);
	}
	
}
