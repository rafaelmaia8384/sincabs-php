<?php

	if (!empty($_POST['id_usuario']) 		&&
        !empty($_POST['id_suspeito']) 		&&
        !empty($_POST['index'])      		&&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $id_suspeito = DBEscape($_POST['id_suspeito']);
		$online_hash = DBEscape($_POST['online_hash']);
        $index = DBEscape($_POST['index']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            $search_limit = GetSearchLimit();

            $limit = ($index - 1) * $search_limit;

            $result = DBRead('tb_suspeitos_comentario INNER JOIN tb_usuarios ON tb_suspeitos_comentario.id_usuario = tb_usuarios.id_usuario', "WHERE tb_suspeitos_comentario.id_suspeito = {$id_suspeito} AND tb_suspeitos_comentario.item_excluido = 0 ORDER BY tb_suspeitos_comentario.data_registro DESC LIMIT {$limit}, {$search_limit}", 'tb_suspeitos_comentario.id as id, tb_suspeitos_comentario.id_usuario as id_usuario, tb_suspeitos_comentario.comentario as comentario, tb_suspeitos_comentario.data_registro as data_registro, tb_usuarios.img_busca as img_busca');

            if (is_array($result)) {

                $result_array = array('Resultado' => $result);

                $i = count($result);

                Sucesso('Comentários encontrados. N: '.$i, $result_array);
            }
            else {

                Erro('Nenhum comentário.');
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
