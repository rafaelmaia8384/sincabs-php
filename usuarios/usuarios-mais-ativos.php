<?php

	if (!empty($_POST['id_usuario']) 		&&
        !empty($_POST['index'])      		&&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $index = DBEscape($_POST['index']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            $search_limit = GetSearchLimit();

            $limit = ($index - 1) * $search_limit;

			$peso_compartilhado = PONTUACAO_COMPARTILHADO;
			$peso_suspeito = PONTUACAO_PESO_SUSPEITO;
			$peso_busca = PONTUACAO_PESO_BUSCA;
			$peso_acesso = PONTUACAO_PESO_ACESSO;

			//$result = DBReadIndependent("SELECT DISTINCT img_principal, img_busca, id_usuario, instituicao, uf, sincabs_compartilhado, nome_completo, data_registro, ( num_suspeitos * {$peso_suspeito} + total_buscas * {$peso_busca} + total_logins * {$peso_acesso} ) as pontuacao FROM tb_usuarios WHERE analise_documental_concluida = 1 AND img_id_frente_principal != 'fail' AND conta_excluida = 0 ORDER BY ( num_suspeitos * 10 + total_buscas * 5 + total_logins ) DESC LIMIT {$limit}, {$search_limit}");

			$result = DBReadIndependent("SELECT DISTINCT img_principal, img_busca, id_usuario, instituicao, uf, nome_completo, data_registro, ( num_suspeitos * {$peso_suspeito} + total_buscas * {$peso_busca} + total_logins * {$peso_acesso} + sincabs_compartilhado * {$peso_compartilhado} ) as pontuacao FROM tb_usuarios WHERE analise_documental_concluida = 1 AND img_id_frente_principal != 'fail' AND conta_excluida = 0 ORDER BY pontuacao DESC LIMIT {$limit}, {$search_limit}");

			if (is_array($result)) {

				$result_array = array('Resultado' => $result);

				Sucesso('Usuários encontrados.', $result_array);
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

?>
