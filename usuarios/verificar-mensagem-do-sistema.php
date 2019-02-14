<?php

	$result = DBRead('tb_sistema', "WHERE id = 1 LIMIT 1", 'mensagem_sistema');

	if (is_array($result)) {

		$result_array = explode('###', $result[0]['mensagem_sistema']);

		$msg_id = $result_array[0];
		$msg = $result_array[1];

		$result_array = array(

			"msg_id" => $msg_id,
			"Mensagem" => $msg
		);

		Sucesso('Mensagem recebida.', $result_array);
	}
	else {

		Erro('Tabela não encontrada.');
	}

?>
