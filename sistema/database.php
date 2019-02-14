<?php

	function DBEscape($string) {
		
		$link = DBConnect();
		
		return 	mysqli_real_escape_string($link, $string);
	}

	function DBExecute($query) {
		
		$link = DBConnect();
		
		$result = mysqli_query($link, $query . ';') or Fatal();
		
		return $result;
	}
	
	function DBExecuteNoError($query) {
		
		$link = DBConnect();
		
		$result = mysqli_query($link, $query . ';');
		
		return $result;		
	}
	
	function DBExecuteMultiQuery($query) {
		
		$link = DBConnect();
		
		$result = mysqli_multi_query($link, $query);
		
		while (mysqli_more_results($link) && mysqli_next_result($link));
	}
	
	function DBReadIndependent($query) {
		
		$result = DBExecute($query);
		
		if (!mysqli_num_rows($result)) {

			return false;
		}
		else {
			
			while ($res = mysqli_fetch_assoc($result)) {
				
				$data[] = $res;
			}
			
			return $data;
		}
	}
	
	function DBRead($table, $params = null, $fields = '*') {
		
		$params = ($params) ? " {$params}" : null; 
		
		$query = "SELECT DISTINCT {$fields} FROM {$table}{$params}";
		$result = DBExecute($query);
		
		if (!mysqli_num_rows($result)) {

			return false;
		}
		else {
			
			while ($res = mysqli_fetch_assoc($result)) {
				
				$data[] = $res;
			}
			
			return $data;
		}
	}
	
	function DBCreate($table, array &$data) {
		
		$fields = implode(', ', array_keys($data));
		$values = "'". implode("', '", $data) . "'";
		
		$query = "INSERT INTO {$table} ( {$fields} ) VALUES ( {$values} )";
		
		return DBExecute($query);
	}
	
	function DBCreateNoError($table, array &$data) {
		
		$fields = implode(', ', array_keys($data));
		$values = "'". implode("', '", $data) . "'";
		
		$query = "INSERT INTO {$table} ( {$fields} ) VALUES ( {$values} )";
		
		return DBExecuteNoError($query);
	}
	
	/*function DBUpDate($table, array &$data, $where = null) {
		
		foreach ($data as $key => $value) {
			
			$fields[] = "{$key} = '{$value}'";
		}
		
		$fields = implode(', ', $fields);
		$where = ($where) ? " WHERE {$where}" : null;
		
		$query = "UPDATE {$table} SET {$fields}{$where}";
		
		return DBExecute($query); 
	}
	
	function DBDelete($table, $where = null) {
		
		$where = ($where) ? " WHERE {$where}" : null;
		
		$query = "DELETE FROM {$table}{$where}";
		
		return DBExecute($query);
	}*/

?>
