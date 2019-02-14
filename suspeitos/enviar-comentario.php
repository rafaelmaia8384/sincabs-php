<?php

	if (!empty($_POST['id_usuario']) 		&&
        !empty($_POST['id_suspeito']) 		&&
        !empty($_POST['comentario'])   		&&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $id_suspeito = DBEscape($_POST['id_suspeito']);
		$online_hash = DBEscape($_POST['online_hash']);
        $comentario = DBEscape($_POST['comentario']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            $protect_hash = GetProtectHash($id_usuario);

            $agora = date('Y-m-d H:i:s', time());

            $array_comentario = array(

                'id_suspeito'       => $id_suspeito,
                'id_usuario'        => $id_usuario,
                'comentario'        => $comentario,
                'data_registro'     => $agora,
                'item_excluido'     => 0,
                'protect_hash'     => $protect_hash
            );

            DBCreate('tb_suspeitos_comentario', $array_comentario);

            Sucesso('Comentário enviado.');
		}
		else {

			Erro(ERROR_OFFLINE);
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

?>
