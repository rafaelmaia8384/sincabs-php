<?php

	if (!empty($_POST['img_principal']) 			&&
		!empty($_POST['img_busca'])		 			&&
		!empty($_POST['nome_completo']) 			&&
		!empty($_POST['cpf']) 						&&
		!empty($_POST['email']) 					&&
		!empty($_POST['senha']) 					&&
		!empty($_POST['instituicao']) 				&&
		!empty($_POST['uf']) 						&&
		!empty($_POST['matricula']) 				&&
		!empty($_POST['telefone'])	 				&&
		!empty($_POST['id_aparelho'])) {

		$img_principal = DBEscape($_POST['img_principal']);
		$img_busca = DBEscape($_POST['img_busca']);
		$nome_completo = DBEscape($_POST['nome_completo']);
		$cpf = DBEscape($_POST['cpf']);
		$email = DBEscape($_POST['email']);
		$senha = DBEscape($_POST['senha']);
		$instituicao = DBEscape($_POST['instituicao']);
		$uf = DBEscape($_POST['uf']);
		$matricula = DBEscape($_POST['matricula']);
		$id_aparelho = DBEscape($_POST['id_aparelho']);
		$telefone = DBEscape($_POST['telefone']);

		if (!ValidaCPF($cpf)) {

			if ($img_busca != 'null' && $img_principal != 'null') {

				DeleteImagemUsr($img_busca);
				DeleteImagemUsr($img_principal);
			}

			Erro(ERROR_CPF);
		}
		elseif (!ValidaEmail($email)) {

			if ($img_busca != 'null' && $img_principal != 'null') {

				DeleteImagemUsr($img_busca);
				DeleteImagemUsr($img_principal);
			}

			Erro(ERROR_EMAIL);
		}
		elseif (!ValidaSenha($senha)) {

			if ($img_busca != 'null' && $img_principal != 'null') {

				DeleteImagemUsr($img_busca);
				DeleteImagemUsr($img_principal);
			}

			Erro(ERROR_SENHA);
		}
		elseif (!ValidaCadastroCPF($cpf)) {

			if ($img_busca != 'null' && $img_principal != 'null') {

				DeleteImagemUsr($img_busca);
				DeleteImagemUsr($img_principal);
			}

			Erro('Não foi possível realizar o cadastro. O número do CPF já está cadastrado no sistema.');
		}

		$senha = hash('sha512', $senha);
		$agora = date('Y-m-d H:i:s', time());

		$id_usuario = 0;

		do {

			$id_usuario = GenerateId();

			$result = DBRead('tb_usuarios', "WHERE id_usuario = '$id_usuario' LIMIT 1", 'id');

		} while (is_array($result));

		$protect_hash = GenerateHash();

		require 'sistema/metaphonePTBR.php';

		$metaphone = new Metaphone();

		$nome_completo_soundex = $metaphone->getPhraseMetaphone($nome_completo);

		$nome_completo = ucwords(strtolower(trim($nome_completo)));

		$usuario = array(

			'id_usuario'					=> $id_usuario,

			'img_principal' 				=> $img_principal,
			'img_busca'						=> $img_busca,

			'img_id_frente_principal'		=> 'null',
			'img_id_frente_busca'			=> 'null',
			'img_id_verso_principal'		=> 'null',
			'img_id_verso_busca'			=> 'null',

			'analise_documental_concluida'	=> 0,

			'nome_completo'					=> $nome_completo,
			'nome_completo_soundex'			=> $nome_completo_soundex,

			'cpf'							=> $cpf,
			'email_cadastro'				=> $email,
			'email_institucional'			=> '',
			'email_institucional_codigo'	=> 0,
			'telefone'						=> $telefone,
			'senha'							=> $senha,
			'instituicao'					=> $instituicao,
			'uf'							=> $uf,
			'matricula'						=> $matricula,

			'data_registro'					=> $agora,
			'ultima_atividade'				=> $agora,
			'ultima_atualizacao_documental'	=> $agora,

			'id_aparelho'					=> $id_aparelho,
			'total_logins'					=> 0,
			'total_buscas'					=> 0,

			'admin'							=> 0,

			'conta_excluida'				=> 0,
			'conta_bloqueada'				=> 0,
			'motivo_bloqueio'				=> '',

			'online_hash'					=> '',
			'protect_hash'					=> $protect_hash,

			'num_suspeitos'					=> 0
		);

		DBCreate('tb_usuarios', $usuario);

		$query = '';

		if ($instituicao == 1) {

			$query = "UPDATE tb_sistema SET total_usuarios_pc = total_usuarios_pc + 1 WHERE id = 1 LIMIT 1";
		}
		elseif ($instituicao == 2) {

			$query = "UPDATE tb_sistema SET total_usuarios_pm = total_usuarios_pm + 1 WHERE id = 1 LIMIT 1";
		}
		elseif ($instituicao == 3) {

			$query = "UPDATE tb_sistema SET total_usuarios_pf = total_usuarios_pf + 1 WHERE id = 1 LIMIT 1";
		}
		elseif ($instituicao == 4) {

			$query = "UPDATE tb_sistema SET total_usuarios_prf = total_usuarios_prf + 1 WHERE id = 1 LIMIT 1";
		}
		elseif ($instituicao == 5) {

			$query = "UPDATE tb_sistema SET total_usuarios_agp_estadual = total_usuarios_agp_estadual + 1 WHERE id = 1 LIMIT 1";
		}
		else {

			$query = "UPDATE tb_sistema SET total_usuarios_agp_federal = total_usuarios_agp_federal + 1 WHERE id = 1 LIMIT 1";
		}

		DBExecuteNoError($query);

		Sucesso("Cadastro realizado com sucesso.\n\nUtilize seu CPF e senha para continuar.");
	}
	else {

		sincabsDie('Acesso negado.');
	}

?>
