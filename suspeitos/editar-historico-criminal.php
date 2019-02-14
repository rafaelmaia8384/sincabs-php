<?php

	if (!empty($_POST['id_usuario']) 		&&
        !empty($_POST['historico'])			&&
        !empty($_POST['id_suspeito'])		&&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $id_suspeito = DBEscape($_POST['id_suspeito']);
		$online_hash = DBEscape($_POST['online_hash']);
        $historico = DBEscape($_POST['historico']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            $protect_hash = GetProtectHash($id_usuario);

            $result = DBRead('tb_suspeitos', "WHERE id_suspeito = {$id_suspeito} LIMIT 1", 'protect_hash');

            if (is_array($result)){

                if ($result[0]['protect_hash'] != $protect_hash && !isAdmin($id_usuario)) {

                    sincabsDie('Acesso negado.');
                }

                DBExecute("UPDATE tb_suspeitos SET historico_criminal = {$historico} WHERE id_suspeito = {$id_suspeito} LIMIT 1");

                Sucesso('Histórico criminal editado com sucesso.');
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

	function isAdmin($id_usuario) {

        $result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario} AND conta_excluida = 0 LIMIT 1", 'admin');

        if (is_array($result)) {

            if ($result[0]['admin'] == 1) {

                return true;
            }
        }

        return false;
    }

?>
