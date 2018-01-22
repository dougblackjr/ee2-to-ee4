<?php

// Initialize variables
chdir('..');
define("DOCROOT", getcwd());
require_once(DOCROOT . '/controllers/ViewController.php');

$viewer = new ViewController();

// Get data
echo $viewer->view();