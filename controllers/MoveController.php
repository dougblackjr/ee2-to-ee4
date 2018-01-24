<?php

class MoveController
{

	public $databaseOne;
	public $databaseTwo;
	public $requestData;
	public $viewData;

	function __construct()
	{

		// Get config files
		require_once(DOCROOT . '/config/db1.php');
		require_once(DOCROOT . '/config/db2.php');

		// Connect and Set DBs
		$this->databaseOne = $this->connectToDB($db1);
		$this->databaseTwo = $this->connectToDB($db2);

	}

	function connectToDB($db)
	{


		$conn = new mysqli($db['servername'], $db['username'], $db['password'], $db['dbname']);

		if($conn->connect_error || is_null($conn)) {
			
			die('Error establishing MySQL connection: ' . $conn->connect_error);

		}

		return $conn;

	}

	public function move($request)
	{

		$initRequest = $this->initializeFields($request);
		
		foreach ($initRequest as $field) {
			
			$getQuery = $this->getMoveDBData($field);

		}

	}

	function initializeFields($request)
	{

		// Remove empty data from Request data
		$filteredRequest = array_filter($request);
		
		$db1_fields = $this->getViewDBData();

		$fieldsToMove = array();

		foreach ($filteredRequest as $db1name => $db2name) {
			
			$fieldNumber = str_replace('dbfield_', '', $db1name);

			// Get array for filtered Request
			foreach ($db1_fields as $dbf) {
				
				if($dbf['id'] == $fieldNumber) {

					$foundField = $dbf;

					$foundField['db2_field_id'] = $db2name;

					$foundField['db2_table'] = 'exp_channel_data_field_' . $db2name;

					$fieldsToMove[] = $foundField;

				}

			}

		}

		return $fieldsToMove;

	}

	function getMoveDBData($dbInfo)
	{

		// Set up query
		switch ($dbInfo['type']) {
			case 'relationship':
				$data = $this->getRelationshipData($dbInfo);
				break;
			
			case 'grid':
				$data = $this->getGridData($dbInfo);
				break;

			case 'matrix':
			case 'playa':
				die('I TOLD YOU NO ' . $dbInfo['type'] . ' FIELDS! But some just want to watch the world burn.');
				break;

			default:
				$data = $this->getRegularData($dbInfo);
				break;
		}

	}

	private function getRelationshipData($dbInfo)
	{

		$query = 'SELECT `relationship_id`, `parent_id`, `child_id`, `order`, `field_id`, `grid_field_id`, `grid_col_id`, `grid_row_id` FROM ' . $dbInfo['table'];

		$results = $this->databaseOne->query($query);

		$outputData = array();

		while($row = $results->fetch_assoc()) {

			$outputData[] = $row;

		}

		$queryStart = 'INSERT INTO `' . $dbInfo['db2_table'] . '` (`entry_id`, `field_ft_' . $dbInfo['db2_field_id'] . '`) VALUES(';

		$insertQuery = $queryStart;

		$count = 0;

		$last = end($outputData);

		foreach ($outputData as $oDataRow) {
			
			$keys = array_keys($oDataRow);

			$insertQuery .= "'" . $oDataRow['parent_id'] . "','xhtml'";

			if($count >= 50 || (int)$oDataRow['parent_id'] == (int)$last['parent_id']) {

				$insertQuery .= ');';

				$count = 0;

				// QUERY
				if($this->databaseTwo->query($insertQuery) !== TRUE) {

					$errorString = "BORKED ON: {$insertQuery} \nERROR: {$this->databaseTwo->error}";
					die($errorString);

				}

				$insertQuery = $queryStart;

			} else {
				
				$insertQuery .= '),(';

				$count++;

			}

		}

		// Then add these to relationships
		$queryStart = 'INSERT INTO `exp_relationships` (`relationship_id`, `parent_id`, `child_id`, `order`, `field_id`, `grid_field_id`, `grid_col_id`, `grid_row_id`) VALUES(';

		$insertQuery = $queryStart;

		$count = 0;

		foreach ($outputData as $oDataRow) {
			
			$insertQuery .= "'" . $oDataRow['relationship_id'] . "','" . $oDataRow['parent_id'] . "','" . $oDataRow['child_id'] . "','" . $oDataRow['order'] . "','" . $oDataRow['field_id'] . "','". $oDataRow['grid_field_id'] . "','". $oDataRow['grid_col_id'] . "','" . $oDataRow['grid_row_id'] . "'";

			if($count >= 50 || (int)$oDataRow['parent_id'] == (int)$last['parent_id']) {

				$insertQuery .= ');';

				$count = 0;

				// QUERY
				if($this->databaseTwo->query($insertQuery) !== TRUE) {

					$errorString = "BORKED ON: {$insertQuery} \nERROR: {$this->databaseTwo->error}";
					die($errorString);

				}

				$insertQuery = $queryStart;

			} else {
				
				$insertQuery .= '),(';

				$count++;

			}

		}

		return null;

	}

	private function getGridData($dbInfo)
	{

	}

	private function getRegularData($dbInfo)
	{

		$query = 'SELECT entry_id, field_id_' . $dbInfo['id'] . ',field_ft_' . $dbInfo['id'] . ' FROM ' . $dbInfo['table'];
		
		$results = $this->databaseOne->query($query);

		$outputData = array();

		while($row = $results->fetch_assoc()) {

			$outputData[] = $row;

		}

		// var_dump($outputData, $dbInfo);
		// die();

		$queryStart = 'INSERT INTO `' . $dbInfo['db2_table'] . '` (`entry_id`, `field_id_' . $dbInfo['db2_field_id'] . '`, `field_ft_' . $dbInfo['db2_field_id'] . '`) VALUES(';

		$insertQuery = $queryStart;

		$count = 0;

		$last = end($outputData);

		foreach ($outputData as $oDataRow) {
			
			$keys = array_keys($oDataRow);

			if(!empty($oDataRow[$keys[1]])) {
				$f1 = "'" . $oDataRow[$keys[1]] . "'";
			} else {
				$f1 = "null";
			}

			if(!empty($oDataRow[$keys[2]])) {
				$f2 = "'" . $oDataRow[$keys[2]] . "'";
			} else {
				$f2 = "null";
			}

			$insertQuery .= "'" . $oDataRow['entry_id'] . "', " . $f1 . ", " . $f2;

			if($count >= 50 || (int)$oDataRow['entry_id'] == (int)$last['entry_id']) {

				$insertQuery .= ');';

				$count = 0;

				// QUERY
				if($this->databaseTwo->query($insertQuery) !== TRUE) {

					$errorString = "BORKED ON: {$insertQuery} \nERROR: {$this->databaseTwo->error}";
					die($errorString);

				}

				$insertQuery = $queryStart;

			} else {
				
				$insertQuery .= '),(';

				$count++;

			}

		}

		return null;

	}

	function moveEntries()
	{

	}

	function returnNextView()
	{

	}

	// PRIVATE FXNS
	function getViewDBData()
	{

		// ========= INITIALIZE =========
		$db1_ViewData = $db2_ViewData = $db1_fieldtypes = $db2_fieldtypes = $db1_fields = $db2_fields = array();
		$fieldtypeQuery = 'SELECT fieldtype_id, name FROM exp_fieldtypes';
		$fielddataQuery = 'SELECT field_id, field_name, field_label, field_type FROM exp_channel_fields';

		// ========= DATABASE 1 (EE2) =========
		// Get exp_fieldtypes (get fieldtype_id, name)
		$db1_results = $this->databaseOne->query($fieldtypeQuery);

		$db1_fieldtypes = $this->getFieldTypes($db1_results);
		
		// Get exp_channel_fields (get field_id, field_label, field_type)
		$db1_results = $this->databaseOne->query($fielddataQuery);
		
		$db1_fields = $this->getFields($db1_results);

		return $db1_fields;

	}

	private function getFieldTypes($dbResult)
	{

		$returnData = array();

		while($row = $dbResult->fetch_assoc()) {

			$returnData[] = array(
				'id' => $row['fieldtype_id'],
				'name' => $row['name']
			);
		}

		return $returnData;

	}

	private function getFields($dbResult)
	{

		$returnData = array();

		while($row = $dbResult->fetch_assoc()) {

			switch ($row['field_type']) {
				case 'relationship':
					$table = 'exp_relationships';
					break;
				
				case 'grid':
					$table = 'exp_channel_grid_field_' . $row['field_id'];
					break;

				case 'matrix':
				case 'playa':
					$table = NULL;
					break;

				default:
					$table = 'exp_channel_data';
					break;
			}

			$returnData[] = array(
				'id' => $row['field_id'],
				'name' => $row['field_name'],
				'type' => $row['field_type'],
				'table' => $table
			);

		}

		return $returnData;

	}
}