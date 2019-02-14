<?php

	if (!empty($_POST['id_usuario']) 		    &&
        !empty($_POST['id_suspeito'])           &&
        !empty($_POST['motivo_denuncia'])       &&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $id_suspeito = DBEscape($_POST['id_suspeito']);
        $motivo_denuncia = DBEscape($_POST['motivo_denuncia']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            $result = DBRead('tb_suspeitos_denuncia', "WHERE id_usuario = {$id_usuario} AND id_suspeito = {$id_suspeito} AND item_excluido = 0 LIMIT 1", 'id');

            if (is_array($result)) {

                Erro('Você já denunciou este perfil.');
            }

            $agora = date('Y-m-d H:i:s', time());

            $denuncia = array(

                'id_usuario'            => $id_usuario,
                'id_suspeito'           => $id_suspeito,
                'motivo_denuncia'       => $motivo_denuncia,
				'item_excluido'			=> 0,
                'data_registro'         => $agora
            );

            DBCreate('tb_suspeitos_denuncia', $denuncia);

			Sucesso('Sua denúncia foi registrada com sucesso.');
		}
		else {

			Erro(ERROR_OFFLINE);
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

?>
