<?php
namespace Claromentis\Social\Configuration;

use Claromentis\Social\Configuration;

/**
 * This factory class uses some logic to determine which builder instance
 * should be created.
 *
 * @author Nathan Crause <nathan at crause.name>
 */
class BuilderFactory {
	
	public static function getBuilder() {
		global $cfg_legacy_override;
		
		self::registerProviders();
		
		if ($cfg_legacy_override 
				&& (self::legacyFacebookExists()
						|| self::legacyTwitterExists())) {
			return new Builder\Legacy();
		}
		else {
			return new Builder\Advanced();
		}
	}
	
	/**
	 * Detects if there is legacy facebok configurations exposed.
	 * 
	 * @return boolean <code>true</code> if there are legacy configurations
	 */
	public static function legacyFacebookExists() {
		return key_exists('cfg_social_stream_facebook_page', $GLOBALS)
				&& key_exists('cfg_facebook_app_id', $GLOBALS)
				&& key_exists('cfg_facebook_app_secret', $GLOBALS);
	}

	/**
	 * Detects if there is legacy twitter configurations exposed.
	 * 
	 * @return boolean <code>true</code> if there are legacy configurations
	 */
	public static function legacyTwitterExists() {
		return key_exists('cfg_social_stream_twitter_streams', $GLOBALS)
				&& key_exists('cfg_twitter_consumer_key', $GLOBALS)
				&& key_exists('cfg_twitter_consumer_secret', $GLOBALS);
	}
	
	private static function registerProviders() {
		global $cfg_social_mapping;
		
		foreach ($cfg_social_mapping as $provider => $fqn) {
			Configuration::registerProvider($provider, $fqn);
		}
	}
	
}
