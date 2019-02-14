<?php

	if (!empty($_POST['id_usuario']) 		&&
        !empty($_POST['id_comentario'])		&&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
		$online_hash = DBEscape($_POST['online_hash']);
        $id_comentario = DBEscape($_POST['id_comentario']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            $protect_hash = GetProtectHash($id_usuario);

            $result = DBRead('tb_suspeitos_comentario', "WHERE id = {$id_comentario} LIMIT 1", 'protect_hash, item_excluido');

            if (is_array($result)){

                if ($result[0]['item_excluido'] == 1) {

                    Erro('Este comentário já foi excluído.');
                }

                if ($result[0]['protect_hash'] != $protect_hash) {

                    sincabsDie('Acesso negado.');
                }

                DBExecute("UPDATE tb_suspeitos_comentario SET item_excluido = 1 WHERE id = {$id_comentario} LIMIT 1");

                Sucesso('Comentário excluído.');
            }
            else {

                Erro('Comentário não encontrado.');
            }
		}
		else {

			Erro(ERROR_OFFLINE);
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

?>
