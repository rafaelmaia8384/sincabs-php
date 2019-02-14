<?php

    if (!empty($_POST['id_usuario'])    &&
        !empty($_POST['codigo'])        &&
        !empty($_POST['online_hash'])) {

        $id_usuario = DBEscape($_POST['id_usuario']);
        $codigo = DBEscape($_POST['codigo']);
        $online_hash = DBEscape($_POST['online_hash']);

        if (UsuarioVerificado($id_usuario, $online_hash)) {

            $result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario} LIMIT 1", 'email_institucional_codigo');

            if ($result[0]['email_institucional_codigo'] == $codigo) {

                DBExecute("UPDATE tb_usuarios SET analise_documental_concluida = 1 WHERE id_usuario = {$id_usuario} LIMIT 1");

                Sucesso("Parabéns!\n\nSeu acesso à plataforma foi liberado com sucesso.");
            }
            else {

                Erro('Código de confirmação inválido.');
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
