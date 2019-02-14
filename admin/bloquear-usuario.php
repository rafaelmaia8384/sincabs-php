<?php

	if (!empty($_POST['id_usuario']) 		&&
        !empty($_POST['id_usuario_perfil']) &&
        !empty($_POST['motivo'])            &&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $id_usuario_perfil = DBEscape($_POST['id_usuario_perfil']);
        $motivo = DBEscape($_POST['motivo']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            if (!isAdmin($id_usuario)) {

				sincabsDie('Acesso negado.');
			}

			$result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario_perfil} LIMIT 1", 'analise_documental_concluida, conta_bloqueada, motivo_bloqueio');

			if (is_array($result)) {

                if ($result[0]['analise_documental_concluida'] == 0) {

                    Erro('A análise documental deste solicitante ainda não foi concluída.');
                }

                if ($result[0]['conta_bloqueada'] == 1) {

                    Erro("Este usuário já está bloqueado.\n\nMotivo:\n\n".$result[0]['motivo_bloqueio']);
                }

                DBExecute("UPDATE tb_usuarios SET conta_bloqueada = 1, motivo_bloqueio = '$motivo' WHERE id_usuario = {$id_usuario_perfil} LIMIT 1");

                Sucesso('Usuário bloqueado com sucesso.');
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
