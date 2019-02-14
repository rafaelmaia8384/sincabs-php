<?php

	if (!empty($_POST['id_usuario']) 		&&
        !empty($_POST['id_usuario_perfil']) &&
        !empty($_POST['analise'])           &&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $id_usuario_perfil = DBEscape($_POST['id_usuario_perfil']);
        $analise = DBEscape($_POST['analise']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            if (!isAdmin($id_usuario)) {

				sincabsDie('Acesso negado.');
			}

			$result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario_perfil} LIMIT 1", 'analise_documental_concluida, img_principal, img_busca, img_id_frente_principal, img_id_frente_busca, img_id_verso_principal, img_id_verso_busca');

			if (is_array($result)) {

                if ($result[0]['analise_documental_concluida'] == 1) {

                    Erro('A análise documental deste solicitante já foi concluída.');
                }

                if ($analise != 1 && $analise != 2) {

                    sincabsDie('Acesso negado.');
                }

                $img_principal = $result[0]['img_principal'];
                $img_busca = $result[0]['img_busca'];

                $img_id_frente_principal = $result[0]['img_id_frente_principal'];
                $img_id_frente_busca = $result[0]['img_id_frente_busca'];
                $img_id_verso_principal = $result[0]['img_id_verso_principal'];
                $img_id_verso_busca = $result[0]['img_id_verso_busca'];

                if ($analise == 2) { // análise indeferida!

                    DBExecute("UPDATE tb_usuarios SET img_id_frente_principal = 'fail', img_id_frente_busca = 'fail', img_id_verso_principal = 'fail', img_id_verso_busca = 'fail' WHERE id_usuario = {$id_usuario_perfil} LIMIT 1");

                    unlink(GetImagePathInServer($img_principal));
                    unlink(GetImagePathInServer($img_busca));

                    unlink(GetImagePathInServer($img_id_frente_principal));
                    unlink(GetImagePathInServer($img_id_frente_busca));
                    unlink(GetImagePathInServer($img_id_verso_principal));
                    unlink(GetImagePathInServer($img_id_verso_busca));
                }

                DBExecute("UPDATE tb_usuarios SET analise_documental_concluida = 1 WHERE id_usuario = {$id_usuario_perfil} LIMIT 1");

                Sucesso('Análise enviada.');
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
