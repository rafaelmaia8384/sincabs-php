<?php

	if (!empty($_POST['id_usuario']) 		&&
        !empty($_POST['index'])      		&&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $index = DBEscape($_POST['index']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

			if (!isAdmin($id_usuario)) {

				sincabsDie('Acesso negado.');
			}

            $search_limit = GetSearchLimit();

            $limit = ($index - 1) * $search_limit;

			$result = DBReadIndependent("SELECT DISTINCT tb_sugestoes.id_usuario as id_usuario, tb_sugestoes.sugestao as sugestao, tb_sugestoes.data_registro as data_registro, tb_usuarios.img_busca as img_busca FROM tb_sugestoes INNER JOIN tb_usuarios ON tb_sugestoes.id_usuario = tb_usuarios.id_usuario WHERE tb_sugestoes.item_excluido = 0 ORDER BY tb_sugestoes.data_registro DESC LIMIT {$limit}, {$search_limit}");

			if (is_array($result)) {

				$result_array = array('Resultado' => $result);

				Sucesso('Sugestões encontradas.', $result_array);
			}
			else {

				Erro('Nenhum resultado.');
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
