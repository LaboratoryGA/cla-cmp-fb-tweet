<?php
\Claromentis\Social\ClassLoader::register('Facebook', realpath(__DIR__ . '/lib/facebook-php-sdk-v4-4.0-dev/src'));
\Claromentis\Social\ClassLoader::register('TwitterOAuth', realpath(__DIR__ . '/lib/TwitterOAuth-2/src'), true);
\Claromentis\Social\ClassLoader::register('LinkedIn', realpath(__DIR__ . '/lib/PHP-LinkedIn-SDK-master'));

function legacy_facebook_exists() {
	return key_exists('cfg_social_stream_facebook_page', $GLOBALS)
			&& key_exists('cfg_facebook_app_id', $GLOBALS)
			&& key_exists('cfg_facebook_app_secret', $GLOBALS);
}

function legacy_twitter_exists() {
	return key_exists('cfg_social_stream_twitter_streams', $GLOBALS)
			&& key_exists('cfg_twitter_consumer_key', $GLOBALS)
			&& key_exists('cfg_twitter_consumer_secret', $GLOBALS);
}

// load the custom configuration for this module
ClaApplication::LoadConfig('social');

// construct enhanced configuration object using overly simplified
// $cfg_social_XXX configuration options
/* @var $cfg_social Claromentis\Social\Configuration */
global $cfg_social;

// Look for legacy configurations, and merge them into $cfg_social

// if there are legacies present
if ((legacy_facebook_exists() || legacy_twitter_exists()) && $cfg_social->getPurgeOnLegacy()) {
	$cfg_social->purge();
}

// check for legacy facebook configuration
if (legacy_facebook_exists()) {
	// add peculiar "fb" reference used in legacy implementation
	\Claromentis\Social\Configuration::registerProvider('fb', 'Claromentis\Social\Configuration\Provider\Facebook');
	
	foreach ($GLOBALS['cfg_social_stream_facebook_page'] as $page) {
		$resource = trim($page, '/');
		
		$cfg_social->addFacebook("legacy-facebook-{$resource}")
				->setAppID($GLOBALS['cfg_facebook_app_id'])
				->setAppSecret($GLOBALS['cfg_facebook_app_secret'])
				->setResource($resource);
		// also add in the legacy "FB" reference (but leave it as "default")
		$cfg_social->addProviderInstance('fb')
				->setAppID($GLOBALS['cfg_facebook_app_id'])
				->setAppSecret($GLOBALS['cfg_facebook_app_secret'])
				->setResource($resource);
	}
}

// check for legacy twitter configuration
if (legacy_twitter_exists()) {
	foreach ($GLOBALS['cfg_social_stream_twitter_streams'] as $screenName) {
		$cfg_social->addTwitter("legacy-twitter-{$screenName}")
				->setConsumerKey($GLOBALS['cfg_twitter_consumer_key'])
				->setConsumerSecret($GLOBALS['cfg_twitter_consumer_secret'])
				->setScreenName($screenName);
	}
}
