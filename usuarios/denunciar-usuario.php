<?php

	if (!empty($_POST['id_usuario']) 		    &&
        !empty($_POST['id_usuario_denunciado']) &&
        !empty($_POST['motivo_denuncia'])       &&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $id_usuario_denunciado = DBEscape($_POST['id_usuario_denunciado']);
        $motivo_denuncia = DBEscape($_POST['motivo_denuncia']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            $result = DBRead('tb_usuarios_denuncia', "WHERE id_usuario = {$id_usuario} AND id_usuario_denunciado = {$id_usuario_denunciado} AND item_excluido = 0 LIMIT 1", 'id');

            if (is_array($result)) {

                Erro('Você já denunciou este usuário.');
            }

			$result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario_denunciado} AND conta_excluida = 0 LIMIT 1", 'conta_bloqueada');

			if (is_array($result)) {

				if ($result[0]['conta_bloqueada'] == 1) {

					Erro('Neste momento o usuário já está impedido de utilizar a plataforma.');
				}
			}

            $agora = date('Y-m-d H:i:s', time());

            $denuncia = array(

                'id_usuario'            => $id_usuario,
                'id_usuario_denunciado' => $id_usuario_denunciado,
                'motivo_denuncia'       => $motivo_denuncia,
				'item_excluido'			=> 0,
                'data_registro'         => $agora
            );

            DBCreate('tb_usuarios_denuncia', $denuncia);

			Sucesso('Sua denúncia foi registrada com sucesso.');
		}
		else {

			Erro(ERROR_OFFLINE);
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

?>
