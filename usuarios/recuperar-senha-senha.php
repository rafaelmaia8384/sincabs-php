<?php

	if (!empty($_POST['senha'])			&&
		!empty($_POST['id_aparelho']) 	&&
		!empty($_POST['cpf']) 			&&
        !empty($_POST['matricula']) 	&&
        !empty($_POST['instituicao'])) {

		$senha = DBEscape($_POST['senha']);
		$id_aparelho = DBEscape($_POST['id_aparelho']);
		$cpf = DBEscape($_POST['cpf']);
		$matricula = DBEscape($_POST['matricula']);
		$instituicao = DBEscape($_POST['instituicao']);

		$result = DBRead('tb_usuarios', "WHERE cpf = {$cpf} AND matricula = {$matricula} AND instituicao = {$instituicao} AND conta_excluida = 0 LIMIT 1", 'id_aparelho');

        if (is_array($result)) {

			if ($result[0]['id_aparelho'] == $id_aparelho) {

				$senha = hash('sha512', $senha);

				DBExecute("UPDATE tb_usuarios SET senha = '$senha' WHERE cpf = {$cpf} AND matricula = {$matricula} AND instituicao = {$instituicao} AND conta_excluida = 0 LIMIT 1");

				Sucesso('Sua senha foi alterada com sucesso.');
			}
			else {

				Erro('Acesso negado.');
			}
        }
		else {

			Erro('Acesso negado.');
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

?>
