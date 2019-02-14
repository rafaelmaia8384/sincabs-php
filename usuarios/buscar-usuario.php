<?php

	if (!empty($_POST['id_usuario']) 		    &&
        !empty($_POST['online_hash'])           &&
        !empty($_POST['index'])                 &&
        !empty($_POST['nome'])       		    &&
        !empty($_POST['ocupacao_profissional'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $online_hash = DBEscape($_POST['online_hash']);
        $index = DBEscape($_POST['index']);
        $nome = DBEscape($_POST['nome']);
        $ocupacao_profissional = DBEscape($_POST['ocupacao_profissional']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            $search_limit = GetSearchLimit();

            $limit = ($index - 1) * $search_limit;

            require 'sistema/metaphonePTBR.php';

            $metaphone = new Metaphone();

            $nome_soundex = $metaphone->getPhraseMetaphone($nome);
            $nome_soundex = GetBooleanNames($nome_soundex);

			$peso_suspeito = PONTUACAO_PESO_SUSPEITO;
			$peso_busca = PONTUACAO_PESO_BUSCA;
			$peso_acesso = PONTUACAO_PESO_ACESSO;

			$result = DBReadIndependent("SELECT DISTINCT img_principal, img_busca, id_usuario, instituicao, uf, nome_completo, data_registro, ( num_suspeitos * {$peso_suspeito} + total_buscas * {$peso_busca} + total_logins * {$peso_acesso} ) as pontuacao FROM tb_usuarios WHERE analise_documental_concluida = 1 AND img_id_frente_principal != 'fail' AND conta_excluida = 0 AND instituicao = {$ocupacao_profissional} AND MATCH(nome_completo_soundex) AGAINST('{$nome_soundex}' IN BOOLEAN MODE) ORDER BY ( num_suspeitos * 10 + total_buscas * 5 + total_logins ) DESC LIMIT {$limit}, {$search_limit}");

            if (is_array($result)) {

                $result_array = array('Resultado' => $result);

				DBExecuteNoError("UPDATE tb_usuarios SET total_buscas = total_buscas + 1 WHERE id_usuario = {$id_usuario} AND conta_excluida = 0 LIMIT 1");

                Sucesso('Busca realizada.', $result_array);
            }
            else {

                Erro('Nenhum usuário encontrado.');
            }
		}
		else {

			Erro(ERROR_OFFLINE);
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

    function GetBooleanNames($text) {

		$boolean_names = '';
		$nomes = explode(' ', $text);
		$count = count($nomes);

		for ($a = 0; $a < $count; $a++) {

			$soundex = $nomes[$a];
			$boolean_names .= "+{$soundex} ";
		}

		return trim($boolean_names);
	}

?>
