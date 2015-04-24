<?php
\Claromentis\Social\ClassLoader::register('Facebook', realpath(__DIR__ . '/lib/facebook-php-sdk-v4-4.0-dev/src'));
\Claromentis\Social\ClassLoader::register('TwitterOAuth', realpath(__DIR__ . '/lib/TwitterOAuth-2/src'), true);
\Claromentis\Social\ClassLoader::register('LinkedIn', realpath(__DIR__ . '/lib/PHP-LinkedIn-SDK-master'));

// load the custom configuration for this module
ClaApplication::LoadConfig('social');

// construct enhanced configuration object using overly simplified
// $cfg_social_XXX configuration options
global $cfg_social;

$cfg_social = \Claromentis\Social\Configuration\BuilderFactory::getBuilder()->build();
