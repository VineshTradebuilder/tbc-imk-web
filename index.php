<?php

ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    
    
include_once 'vendor/autoload.php';
use TBC\IMK\WEB\ImkServiceProvider;
$imkObj = new ImkServiceProvider();
$imkObj->setApiGroup("group");
$imkObj->setApiKey("key");
$imkObj->setApiUser("user");
$imkObj->setApiUrl("Url");

$imkObj->printDetail();