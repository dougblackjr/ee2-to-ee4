<?php

class ViewController
{

	public $databaseOne;
	public $databaseTwo;

	function __construct()
	{

		// Get config files

		require_once(DOCROOT . '/config/db1.php');
		require_once(DOCROOT . '/config/db2.php');

		// Connect and Set DBs
		$this->databaseOne = $this->connectToDB($db1);
		$this->databaseTwo = $this->connectToDB($db2);

		// Setup DB Info for each database
		// $this->dbOneFields = $this->dbOneData = $this->dbTwoData = $this->dbTwoFields = array();
	}

	/**
	 * Connects to database
	 * @param  [array] $db [DB set up information, set in config/db1 or db2]
	 * @return [mysqli object]
	 */
	function connectToDB($db)
	{


		$conn = new mysqli($db['servername'], $db['username'], $db['password'], $db['dbname']);

		if($conn->connect_error || is_null($conn)) {
			
			die('Error establishing MySQL connection: ' . $conn->connect_error);

		}

		return $conn;

	}

	public function view()
	{

		return json_encode($this->getViewDBData());

	}

	function getViewDBData()
	{

		// ========= INITIALIZE =========
		$db1_fieldtypes = $db2_fieldtypes = $db1_fields = $db2_fields = array();
		$fieldtypeQuery = 'SELECT fieldtype_id, name FROM exp_fieldtypes';
		$fielddataQuery = 'SELECT field_id, field_name, field_label, field_type FROM exp_channel_fields';

		// ========= DATABASE 1 (EE2) =========
		// Get exp_fieldtypes (get fieldtype_id, name)
		$db1_results = $this->databaseOne->query($fieldtypeQuery);

		$db1_fieldtypes = $this->getFieldTypes($db1_results);
		
		// Get exp_channel_fields (get field_id, field_label, field_type)
		$db1_results = $this->databaseOne->query($fielddataQuery);
		
		$db1_fields = $this->getFields($db1_results);
		
		// ========= DATABASE 2 (EE4) =========
		// Get exp_fieldtypes (get fieldtype_id, name)
		$db2_results = $this->databaseTwo->query($fieldtypeQuery);

		$db2_fieldtypes = $this->getFieldTypes($db2_results);
		
		// Get exp_channel_fields (get field_id, field_label, field_type)
		$db2_results = $this->databaseTwo->query($fielddataQuery);
		
		$db2_fields = $this->getFields($db2_results);

		// Send return data to VIEW
		$returnData = array(
			'fields1' => $db1_fields,
			'fields2' => $db2_fields
		);

		return $returnData;

	}

	private function getFieldTypes($dbResult)
	{

		$returnData = array();

		while($row = $dbResult->fetch_assoc()) {

			$returnData[$row['fieldtype_id']] = $row['name'];

		}

		return $returnData;

	}

	private function getFields($dbResult)
	{

		$returnData = array();

		while($row = $dbResult->fetch_assoc()) {

			$returnData[$row['field_id']] = $row['field_label'] . '(' . $row['field_type'] . ')';

		}

		return $returnData;

	}

}