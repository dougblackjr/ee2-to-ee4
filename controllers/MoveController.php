<?php

class MoveController
{

	public $databaseOne;
	public $databaseTwo;

	function __construct()
	{

		// Get config files
		require_once(DOCROOT . 'config/db1.php');
		require_once(DOCROOT . 'config/db2.php');

		// Connect and Set DBs
		$this->databaseOne = $this->connectToDB($db1);
		$this->databaseTwo = $this->connectToDB($db2);

		// Setup DB Info for each database
		// $this->dbOneFields = $this->dbOneData = $this->dbTwoData = $this->dbTwoFields = array();
	}

	function connectToDB($db)
	{


		$conn = new mysqli($db['servername'], $db['username'], $db['password'], $db['dbname']);

		if($conn->connect_error || is_null($conn)) {
			
			die('Error establishing MySQL connection: ' . $conn->connect_error);

		}

		return $conn;

	}

	function getFormData()
	{

	}

	function getMoveDBData()
	{

		// ========= INITIALIZE =========

		// ========= DATABASE 1 (EE2) =========
		// Get exp_fieldtypes (get fieldtype_id, name)
		// Get exp_channel_fields (get field_id, field_name, field_type)
		// Foreach field
			// Get table name
			// Get field name

		// ========= DATABASE 2 (EE4) =========
		// Get exp_fieldtypes

	}

	function moveEntries()
	{

	}

	function returnNextView()
	{

	}
}