<?php

    if (!empty($_POST['id_usuario']) &&
        !empty($_POST['protect_hash']) &&
        !empty($_POST['online_hash'])) {

        $id_usuario = DBEscape($_POST['id_usuario']);
        $online_hash = DBEscape($_POST['online_hash']);
        $protect_hash = DBEscape($_POST['protect_hash']);

        if (UsuarioVerificado($id_usuario, $online_hash)) {

            $result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario} AND conta_excluida = 0 LIMIT 1", 'protect_hash, img_principal, img_busca, img_id_frente_principal, img_id_frente_busca, img_id_verso_principal, img_id_verso_busca');

            if (is_array($result)) {

                if ($result[0]['protect_hash'] == $protect_hash) {

                    DBExecuteMultiQuery(

                        "UPDATE tb_usuarios SET conta_excluida = 1 WHERE id_usuario = {$id_usuario} LIMIT 1;\n".
                        "UPDATE tb_usuarios_denuncia SET item_excluido = 1 WHERE id_usuario = {$id_usuario};\n".
                        "UPDATE tb_suspeitos_comentario SET item_excluido = 1 WHERE id_usuario = {$id_usuario};"
                    );

                    $img_principal = $result[0]['img_principal'];
                    $img_busca = $result[0]['img_busca'];

                    $img_id_frente_principal = $result[0]['img_id_frente_principal'];
                    $img_id_frente_busca = $result[0]['img_id_frente_busca'];
                    $img_id_verso_principal = $result[0]['img_id_verso_principal'];
                    $img_id_verso_busca = $result[0]['img_id_verso_busca'];

                    $result2 = DBRead('tb_usuarios', "WHERE img_principal = '$img_principal' AND conta_excluida = 0 LIMIT 1");

                    if (!is_array($result2)) {

                        $filenamePath = GetImagePathInServer($img_principal);

                        unlink($filenamePath);
                    }

                    $result2 = DBRead('tb_usuarios', "WHERE img_busca = '$img_busca' AND conta_excluida = 0 LIMIT 1");

                    if (!is_array($result2)) {

                        $filenamePath = GetImagePathInServer($img_busca);

                        unlink($filenamePath);
                    }

                    unlink(GetImagePathInServer($img_id_frente_principal));
                    unlink(GetImagePathInServer($img_id_frente_busca));
                    unlink(GetImagePathInServer($img_id_verso_principal));
                    unlink(GetImagePathInServer($img_id_verso_busca));

                    Sucesso('Perfil excluído');
                }
                else {

                    sincabsDie('Acesso negado.');
                }
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

?>
