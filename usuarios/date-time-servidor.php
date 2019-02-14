<?php

	if (!empty($_POST['id_usuario']) 		&&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            $result_array = array('date_time' => date('Y-m-d H:i:s', time()));

			Sucesso('date_time', $result_array);
		}
		else {

			Erro(ERROR_OFFLINE);
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

?>
