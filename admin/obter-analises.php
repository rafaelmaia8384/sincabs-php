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

			$result = DBReadIndependent("SELECT DISTINCT img_principal, img_busca, id_usuario, instituicao, uf, nome_completo, data_registro FROM tb_usuarios WHERE conta_excluida = 0 AND analise_documental_concluida = 0 AND img_id_frente_principal != 'null'AND img_id_verso_principal != 'null' ORDER BY data_registro ASC LIMIT {$limit}, {$search_limit}");

			if (is_array($result)) {

				$result_array = array('Resultado' => $result);

				Sucesso('Solicitações de análise encontradas.', $result_array);
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
