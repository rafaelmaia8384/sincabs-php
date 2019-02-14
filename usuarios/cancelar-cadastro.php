<?php

	if (!empty($_POST['id_usuario']) &&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

			$result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario} LIMIT 1", 'img_principal, img_busca, img_id_frente_principal, img_id_frente_busca, img_id_verso_principal, img_id_verso_busca');

			$img_principal = $result[0]['img_principal'];
			$img_busca = $result[0]['img_busca'];

			$img_id_frente_principal = $result[0]['img_id_frente_principal'];
			$img_id_frente_busca = $result[0]['img_id_frente_busca'];
			$img_id_verso_principal = $result[0]['img_id_verso_principal'];
			$img_id_verso_busca = $result[0]['img_id_verso_busca'];

			unlink(GetImagePathInServer($img_principal));
			unlink(GetImagePathInServer($img_busca));

			unlink(GetImagePathInServer($img_id_frente_principal));
			unlink(GetImagePathInServer($img_id_frente_busca));
			unlink(GetImagePathInServer($img_id_verso_principal));
			unlink(GetImagePathInServer($img_id_verso_busca));

			DBExecute("UPDATE tb_usuarios SET conta_excluida = 1 WHERE id_usuario = '$id_usuario' LIMIT 1");

			Sucesso('Seu cadastro foi cancelado.');
		}
		else {

			Erro(ERROR_OFFLINE);
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

?>
