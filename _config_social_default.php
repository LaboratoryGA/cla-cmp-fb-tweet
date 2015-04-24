<?php
use Claromentis\Social\Configuration;

// register all the default social media services
Configuration::registerProvider('facebook', 'Claromentis\Social\Configuration\Provider\Facebook');
Configuration::registerProvider('twitter', 'Claromentis\Social\Configuration\Provider\Twitter');
Configuration::registerProvider('linkedin', 'Claromentis\Social\Configuration\Provider\LinkedIn');
Configuration::registerProvider('google+', 'Claromentis\Social\Configuration\Provider\Google');
Configuration::registerProvider('instagram', 'Claromentis\Social\Configuration\Provider\Instagram');
Configuration::registerProvider('youtube', 'Claromentis\Social\Configuration\Provider\YouTube');

$cfg_social = new Configuration();

$cfg_social->setPurgeOnLegacy(true);

$cfg_social->addFacebook()
		->setAppID('210301132424295')
		->setAppSecret('75407e2280fd24eb147c3d08cd7e340c')
		->setResource('laboratory.ga');

$cfg_social->addTwitter()
		->setConsumerKey('3rVGexalmyASGnADj3osotm1u')
		->setConsumerSecret('9Fi1oJ9H6fZOZ3xYhwsbZ3LP1icn0eHKSmOIbqKijhRfDCl5o0')
		->setScreenName('LaboratoryGa');

$cfg_social->addLinkedIn()
		->setApiKey('75getsqievrak2')
		->setSecretKey('0rZu9La9ISTFWM2r')
		->setUserToken('2ea34eb5-e6da-4eae-832b-bcaae1da1eb0')
		->setUserSecret('98c75c5f-84c8-4eba-934d-27fbfdbf9116')
		/*->setWhat()*/;

//$cfg_social->addProvider('linkedin')
//		->setYakityShmakkity();