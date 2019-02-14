<?php

	if (!empty($_POST['id_usuario'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);

		$result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario} LIMIT 1", 'analise_documental_concluida');

		if (is_array($result)) {

            if ($result[0]['analise_documental_concluida'] == 1) {

				Sucesso('Análise concluída.');
			}
			else {

				Erro('Análise não concluída.');
			}
		}
		else {

			Erro('Usuário não encontrado.');
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

?>
