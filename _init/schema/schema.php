<?php
// This file contains the database schema version 02.01
if (!isset($migrations) || !is_object($migrations))
	die("This file cannot be executed directly");
if ($migrations->GetVersion() > 0)
	throw new Exception("The database is already initialized");
//===========================================================================================



//===========================================================================================
$migrations->SetVersion('02.01');
