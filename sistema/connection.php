<?php

	function DBConnect() {

		static $link;

		if (!isset($link)) {

			$link = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or Erro(ERROR_SISTEMA);

			mysqli_set_charset($link, DB_CHARSET) or Fatal(mysqli_error($link));
		}

		return $link;
	}

	function DBClose() {

		$link = DBConnect();

		mysqli_close($link);
	}

	function sincabsDie($msg = null) {

		DBClose();

		die($msg);
	}

?>
