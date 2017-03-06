<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once "core/Core.php";

$config = require_once 'config/main.php';

\Core::init($config);
