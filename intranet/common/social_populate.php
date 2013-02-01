<?php
require_once('../common/core.php');
require_once('../common/connect.php');
require_once('../common/sessioncheck.php');

$social = new SocialStream();
$social->Go();
