<?php
if (!defined('INSTALL_PROGRESS'))
	die("This file cannot be executed directly");

if (!isset($installer))
	throw new Exception("Install options are not defined");
	/** @var $installer Claromentis\Setup\SetupFacade */

$config_file = new \Claromentis\Setup\ConfigFile();
$config_file->AddText('$cfg_cla_plugins[] = "SocialPlugin";');
