<?php

	if (!empty($_POST['id_suspeito']) 	&&
        !empty($_POST['id_usuario'])    &&
		!empty($_POST['online_hash'])) {

		$id_suspeito = DBEscape($_POST['id_suspeito']);
        $id_usuario = DBEscape($_POST['id_usuario']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

			$result = DBRead('tb_suspeitos_imagem', "WHERE id_suspeito = {$id_suspeito} AND item_excluido = 0", 'img_busca, img_principal, id_usuario');

			if (is_array($result)) {

				$result_array = array('Resultado' => $result);

				Sucesso('Imagens encontradas.', $result_array);
			}
			else {

				Erro('Nenhuma imagem.');
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
