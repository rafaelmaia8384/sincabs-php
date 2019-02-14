<?php

    if (!empty($_POST['id_usuario']) &&
        !empty($_POST['id_suspeito']) &&
        !empty($_POST['protect_hash']) &&
        !empty($_POST['online_hash'])) {

        $id_usuario = DBEscape($_POST['id_usuario']);
        $id_suspeito = DBEscape($_POST['id_suspeito']);
        $online_hash = DBEscape($_POST['online_hash']);
        $protect_hash = DBEscape($_POST['protect_hash']);

        if (UsuarioVerificado($id_usuario, $online_hash)) {

            $result = DBRead('tb_suspeitos', "WHERE id_suspeito = {$id_suspeito} LIMIT 1", 'id_usuario, protect_hash, img_principal, img_busca');

            if (is_array($result)) {

                $id_usuario_autor = $result[0]['id_usuario'];

                if ($result[0]['protect_hash'] == $protect_hash || isAdmin($id_usuario)) {

                    $resul2 = DBRead('tb_suspeitos_imagem', "WHERE id_suspeito = {$id_suspeito} AND item_excluido = 0", 'id');

                    if (is_array($result2)) {

                        for ($a = 0; $a < count($result2); $a++) {

                            $id = $result2[$a]['id'];

                            deletarImagemTbImagem($id);
                        }
                    }

                    $img_principal = $result[0]['img_principal'];
                    $img_busca = $result[0]['img_busca'];

                    DBExecuteMultiQuery(

                        "UPDATE tb_usuarios SET num_suspeitos = num_suspeitos - 1 WHERE id_usuario = {$id_usuario_autor} LIMIT 1;\n".
                        "UPDATE tb_suspeitos SET suspeito_excluido = 1 WHERE id_suspeito = {$id_suspeito} AND id_usuario = {$id_usuario_autor} LIMIT 1;\n".
                        "UPDATE tb_suspeitos_denuncia SET item_excluido = 1 WHERE id_suspeito = {$id_suspeito};\n".
                        "UPDATE tb_suspeitos_comentario SET item_excluido = 1 WHERE id_suspeito = {$id_suspeito};"
                    );

                    $result3 = DBRead('tb_suspeitos', "WHERE img_principal = '$img_principal' AND suspeito_excluido = 0 LIMIT 1");

                    if (!is_array($result3)) {

                        $filenamePath = GetImagePathInServer($img_principal);

                        unlink($filenamePath);
                    }

                    $result3 = DBRead('tb_suspeitos', "WHERE img_busca = '$img_busca' AND suspeito_excluido = 0 LIMIT 1");

                    if (!is_array($result3)) {

                        $filenamePath = GetImagePathInServer($img_busca);

                        unlink($filenamePath);
                    }

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

    function deletarImagemTbImagem($id) {

        DBExecute("UPDATE tb_suspeitos_imagem SET item_excluido = 1 WHERE id = {$id} LIMIT 1");

        $result = DBRead('tb_suspeitos_imagem', "WHERE id = {$id} LIMIT 1", 'img_principal, img_busca');

        if (is_array($result)) {

            $img_principal = $result[0]['img_principal'];
            $img_busca = $result[0]['img_busca'];

            $result2 = DBRead('tb_usuarios_imagem', "WHERE img_principal = '$img_principal' AND item_excluido = 0 LIMIT 1");

            if (!is_array($result2)) {

                $filenamePath = GetImagePathInServer($img_principal);

                unlink($filenamePath);
            }

            $result2 = DBRead('tb_usuarios_imagem', "WHERE img_busca = '$img_busca' AND item_excluido = 0 LIMIT 1");

            if (!is_array($result2)) {

                $filenamePath = GetImagePathInServer($img_busca);

                unlink($filenamePath);
            }
        }
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
