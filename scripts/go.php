<?php

chdir('..');
define("DOCROOT", getcwd());

require_once(DOCROOT . '/controllers/MoveController.php');

// Get all get variables
$request = $_GET;

$move = new MoveController();

$move->move($request);