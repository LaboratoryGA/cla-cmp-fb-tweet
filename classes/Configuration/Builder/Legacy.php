<?php
namespace Claromentis\Social\Configuration\Builder;

use Claromentis\Social\Configuration;
use Claromentis\Social\Configuration\Builder;
use Claromentis\Social\Configuration\BuilderFactory;

/**
 * Constructs a configuration instance based on legacy social configurations.
 *
 * @author Nathan Crause <nathan at crause.name>
 */
class Legacy implements Builder {
	
	public function build() {
		$config = new Configuration();
		
		if (BuilderFactory::legacyFacebookExists()) {
			global $cfg_facebook_app_id, $cfg_facebook_app_secret, 
					$cfg_social_stream_facebook_page;
			
			// add "fb" reference used in legacy implementation
			Configuration::registerProvider('fb', 'Claromentis\Social\Configuration\Provider\Facebook');

			foreach ($cfg_social_stream_facebook_page as $page) {
				$resource = trim($page, '/');

				$config->addFacebook("legacy-facebook-{$resource}")
						->setAppID($cfg_facebook_app_id)
						->setAppSecret($cfg_facebook_app_secret)
						->setResource($resource);
				// also add in the legacy "FB" reference (but leave it as "default")
				$config->addProviderInstance('fb')
						->setAppID($cfg_facebook_app_id)
						->setAppSecret($cfg_facebook_app_secret)
						->setResource($resource);
			}
		}
		
		if (BuilderFactory::legacyTwitterExists()) {
			global $cfg_social_stream_twitter_streams, 
					$cfg_twitter_consumer_key, $cfg_twitter_consumer_secret;
			
			foreach ($cfg_social_stream_twitter_streams as $screenName) {
				$config->addTwitter("legacy-twitter-{$screenName}")
						->setConsumerKey($cfg_twitter_consumer_key)
						->setConsumerSecret($cfg_twitter_consumer_secret)
						->setScreenName($screenName);
			}
		}
		
		return $config;
	}

}
