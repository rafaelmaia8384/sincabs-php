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

			$result = DBReadIndependent("SELECT tb_suspeito.img_principal as img_principal, tb_suspeito.img_busca as img_busca, tb_suspeito.id_suspeito as id_suspeito, tb_suspeito.nome_alcunha as nome_alcunha, tb_suspeito.nome_completo as nome_completo, tb_suspeito.data_registro as data_registro, tb_suspeito.areas_de_atuacao as areas_de_atuacao, COUNT(*) as count FROM tb_suspeitos_denuncia INNER JOIN tb_suspeitos ON tb_suspeitos_denuncia.id_suspeito = tb_suspeitos.id_suspeito WHERE tb_suspeitos_denuncia.item_excluido = 0 AND tb_suspeitos.suspeito_excluido = 0 GROUP BY tb_suspeitos_denuncia.id_suspeito ORDER BY count DESC LIMIT {$limit}, {$search_limit}");

			if (is_array($result)) {

				$result_array = array('Resultado' => $result);

				Sucesso('Denúncias encontradas.', $result_array);
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

		$result = DBRead('tb_suspeitos', "WHERE id_usuario = {$id_usuario} LIMIT 1", 'admin');

		if (is_array($result)) {

			if ($result[0]['admin'] == 1) {

				return true;
			}
		}

		return false;
	}

?>
