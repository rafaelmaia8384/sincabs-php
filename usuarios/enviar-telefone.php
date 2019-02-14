<?php

    if (!empty($_POST['id_usuario'])    &&
        !empty($_POST['telefone'])      &&
        !empty($_POST['online_hash'])) {

        $id_usuario = DBEscape($_POST['id_usuario']);
        $telefone = DBEscape($_POST['telefone']);
        $online_hash = DBEscape($_POST['online_hash']);

        if (UsuarioVerificado($id_usuario, $online_hash)) {

            DBExecute("UPDATE tb_usuarios SET telefone = '$telefone' WHERE id_usuario = {$id_usuario} LIMIT 1");

            Sucesso('Telefone cadastrado com sucesso.');
        }
        else {

            Erro(ERROR_OFFLINE);
        }
    }
    else {

        sincabsDie('Acesso negado.');
    }

?>
