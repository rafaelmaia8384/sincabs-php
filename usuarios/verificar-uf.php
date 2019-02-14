<?php

	if (!empty($_POST['uf'])) {

		$uf = DBEscape($_POST['uf']);

		$result = DBRead('tb_controle_uf', "WHERE id = {$uf} LIMIT 1", 'cadastro_liberado');

        if (is_array($result)) {

			if ($result[0]['cadastro_liberado'] == 1) {

				Sucesso('Cadastro liberado.');
			}
			else {

				Erro('No momento o cadastro não está liberado para seu local de trabalho.');
			}
        }
		else {

			Erro('UF não encontrada.');
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

?>
