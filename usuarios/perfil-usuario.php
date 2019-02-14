<?php

	if (!empty($_POST['id_usuario']) 		&&
        !empty($_POST['id_usuario_perfil']) &&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $id_usuario_perfil = DBEscape($_POST['id_usuario_perfil']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

			$peso_compartilhado = PONTUACAO_COMPARTILHADO;
			$peso_suspeito = PONTUACAO_PESO_SUSPEITO;
			$peso_busca = PONTUACAO_PESO_BUSCA;
			$peso_acesso = PONTUACAO_PESO_ACESSO;

			$result = DBReadIndependent("SELECT DISTINCT id_usuario, img_principal, img_busca, instituicao, uf, nome_completo, email_cadastro, total_buscas, total_logins, ultima_atividade, data_registro, ( num_suspeitos * {$peso_suspeito} + total_buscas * {$peso_busca} + total_logins * {$peso_acesso} + sincabs_compartilhado * {$peso_compartilhado} ) as pontuacao FROM tb_usuarios WHERE id_usuario = {$id_usuario_perfil} LIMIT 1");

			if (is_array($result)) {

				$pontuacao = $result[0]['pontuacao'];

				$result[0]['pontuacao_icon'] = getPontuacaoIcon($pontuacao);
				$result[0]['pontuacao_comentario'] = getPontuacaoComentario($pontuacao);

				$result2 = DBRead('tb_suspeitos', "WHERE id_usuario = {$id_usuario_perfil} AND suspeito_excluido = 0", 'COUNT(id) as num_suspeitos');

				if (is_array($result2)) {

					$result[0]['num_suspeitos'] = $result2[0]['num_suspeitos'];
				}

				$result2 = DBRead('tb_suspeitos_comentario', "WHERE id_usuario = {$id_usuario_perfil} AND item_excluido = 0", 'COUNT(id) as num_comentarios');

				if (is_array($result2)) {

					$result[0]['num_comentarios'] = $result2[0]['num_comentarios'];
				}

				Sucesso('Perfil encontrado.', $result[0]);
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

	function getPontuacaoIcon($pontos) {

		if ($pontos < 10) {

			return "1.png";
		}
		else if ($pontos < 60) {

			return "2.png";
		}
		else if ($pontos < 185) {

			return "3.png";
		}
		else if ($pontos < 385) {

			return "4.png";
		}
		else if ($pontos < 660) {

			return "5.png";
		}
		else if ($pontos < 1010) {

			return "6.png";
		}
		else if ($pontos < 1435) {

			return "7.png";
		}
		else if ($pontos < 1935) {

			return "8.png";
		}
		else if ($pontos < 2510) {

			return "9.png";
		}
		else if ($pontos < 3160) {

			return "10.png";
		}
		else if ($pontos < 3885) {

			return "11.png";
		}
		else {

			return "12.png";
		}
	}

	function getPontuacaoComentario($pontos) {

		if ($pontos < 10) {

			return "Observador#Usuário com pouca participação na plataforma.";
		}
		else if ($pontos < 60) {

			return "Participante#Indícios de interesse por informações da plataforma.";
		}
		else if ($pontos < 185) {

			return "Medalha de bronze#O usuário apresenta interesse em contribuir.";
		}
		else if ($pontos < 385) {

			return "Medalha de prata#Contribuição e participação moderada.";
		}
		else if ($pontos < 660) {

			return "Medalha de ouro#Interesse pleno em contribuir com informações no sistema.";
		}
		else if ($pontos < 1010) {

			return "Troféu de bronze#Destaca-se pelo grau elevado de contribuição.";
		}
		else if ($pontos < 1435) {

			return "Troféu de prata#Interesse em ajudar no crescimento do banco de dados.";
		}
		else if ($pontos < 1935) {

			return "Troféu de ouro#Ajudante e contribuidor do banco de dados.";
		}
		else if ($pontos < 2510) {

			return "Insígnia de bronze#Contribuidor da plataforma Sincabs - grau 1.";
		}
		else if ($pontos < 3160) {

			return "Bandeja de prata#Contribuidor da plataforma Sincabs - grau 2.";
		}
		else if ($pontos < 3885) {

			return "Coroa de ouro#Contribuidor da plataforma Sincabs - grau 3.";
		}
		else {

			return "Colaborador#Nível máximo dos usuários da plataforma.";
		}
	}

?>
