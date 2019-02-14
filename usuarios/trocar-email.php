<?php

    if (!empty($_POST['id_usuario'])    &&
        !empty($_POST['protect_hash'])  &&
        !empty($_POST['email'])         &&
        !empty($_POST['online_hash'])) {

        $id_usuario = DBEscape($_POST['id_usuario']);
        $email = DBEscape($_POST['email']);
        $protect_hash = DBEscape($_POST['protect_hash']);
        $online_hash = DBEscape($_POST['online_hash']);

        if (UsuarioVerificado($id_usuario, $online_hash)) {

            if (!ValidaEmail($email)) {

                Erro('Email inválido.');
            }

            $result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario} AND conta_excluida = 0 LIMIT 1", 'protect_hash');

            if (is_array($result)) {

                if ($result[0]['protect_hash'] == $protect_hash) {

                    DBExecute("UPDATE tb_usuarios SET email_cadastro = '$email' WHERE id_usuario = {$id_usuario} AND conta_excluida = 0 LIMIT 1");

                    Sucesso('Email alterado.');
                }
                else {

                    Erro('Acesso negado.');
                }
            }
            else {

                Erro('Usuário não encontrado.');
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
