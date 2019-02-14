<?php

	if (!empty($_POST['id_usuario']) 		&&
        !empty($_POST['id_usuario_perfil']) &&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $id_usuario_perfil = DBEscape($_POST['id_usuario_perfil']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            if (!isAdmin($id_usuario)) {

				sincabsDie('Acesso negado.');
			}

			$result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario_perfil} LIMIT 1", 'conta_bloqueada');

			if (is_array($result)) {

                if ($result[0]['conta_bloqueada'] == 0) {

                    Erro('Este usuário não está bloqueado.');
                }

                DBExecute("UPDATE tb_usuarios SET conta_bloqueada = 0, motivo_bloqueio = '' WHERE id_usuario = {$id_usuario_perfil} LIMIT 1");

                Sucesso('Usuário desbloqueado com sucesso.');
			}
			else {

				Erro('Perfil não encontrado.');
			}
		}
		else {

			Erro(ERROR_OFFLINE);
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

    function isAdmin($id_usuario) {

        $result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario} LIMIT 1", 'admin');

        if (is_array($result)) {

            if ($result[0]['admin'] == 1) {

                return true;
            }
        }

        return false;
    }

?>
