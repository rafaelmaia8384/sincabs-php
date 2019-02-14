<?php

	if (!empty($_POST['cpf'])) {

		$cpf = DBEscape($_POST['cpf']);

		$result = DBRead('tb_usuarios', "WHERE cpf = '$cpf' AND conta_excluida = 0 LIMIT 1", 'id');

        if (is_array($result)) {

            Erro('O CPF informado já está cadastrado no sistema.');
        }

		Sucesso('CPF checado.');
	}
	else {

		sincabsDie('Acesso negado.');
	}

?>
