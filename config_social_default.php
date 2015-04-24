<?php
// This option dictates if the presence of legacy configurations should make the
// module build towards those legacy configurations, instead of the local
// config_social.php file(s)
$cfg_legacy_override = true;

// This configuration option defines the mapping from provider name to
// configuration class which handles it. To add a new mapping, add the
// following to /intranet/social/config_social.php:
// $cfg_social_mapping['my_provider'] = 'My\Fully\Qualified\Name';
$cfg_social_mapping = [
	'facebook'	=> 'Claromentis\Social\Configuration\Provider\Facebook',
	'twitter'	=> 'Claromentis\Social\Configuration\Provider\Twitter',
	'linkedin'	=> 'Claromentis\Social\Configuration\Provider\LinkedIn',
	'google+'	=> 'Claromentis\Social\Configuration\Provider\Google',
	'instagram'	=> 'Claromentis\Social\Configuration\Provider\Instagram',
	'youtube'	=> 'Claromentis\Social\Configuration\Provider\YouTube'
];

// This configuration option defines all the streams from which we will be
// pulling. Note that if legacy options are detected, the below configuration
// is ignored.
// To add a new stream, add the following to /intranet/social/config_social.php:
// $cfg_social3_streams[] = [
//		'provider'	=> 'provider_name',	// must have been registered in $cfg_social3_mapping
//		'name'		=> 'instance_name',	// (optional) defaults to 'default'
//		// from this point on, the variables are dependent on what is required
//		// by your individual implementation
// ];
$cfg_social_streams = [
	[
		'provider'			=> 'facebook',
		'appID'				=> '210301132424295',
		'appSecret'			=> '75407e2280fd24eb147c3d08cd7e340c',
		'resource'			=> 'laboratory.ga'
	],
	[
		'provider'			=> 'twitter',
		'consumerKey'		=> '3rVGexalmyASGnADj3osotm1u',
		'consumerSecret'	=> '9Fi1oJ9H6fZOZ3xYhwsbZ3LP1icn0eHKSmOIbqKijhRfDCl5o0',
		'screenName'		=> 'LaboratoryGa'
	],
	[
		'provider'			=> 'linkedin',
		'apiKey'			=> '75getsqievrak2',
		'secretKey'			=> '0rZu9La9ISTFWM2r',
		'userToken'			=> '2ea34eb5-e6da-4eae-832b-bcaae1da1eb0',
		'userSecret'		=> '98c75c5f-84c8-4eba-934d-27fbfdbf9116'
	]
];