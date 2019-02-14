<?php

    if (!empty($_POST['id_usuario'])    &&
        !empty($_POST['online_hash'])) {

        $id_usuario = DBEscape($_POST['id_usuario']);
        $online_hash = DBEscape($_POST['online_hash']);

        if (UsuarioVerificado($id_usuario, $online_hash)) {

            $result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario} LIMIT 1", 'sincabs_compartilhado');

            if (is_array($result)) {

                Sucesso('Informação obtida.', $result[0]);
            }
            else {

                sincabsDie('Acesso negado.');
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
