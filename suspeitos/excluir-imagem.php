<?php

    if (!empty($_POST['id_usuario']) &&
        !empty($_POST['id_suspeito']) &&
        !empty($_POST['protect_hash']) &&
        !empty($_POST['img_principal']) &&
        !empty($_POST['online_hash'])) {

        $id_usuario = DBEscape($_POST['id_usuario']);
        $id_suspeito = DBEscape($_POST['id_suspeito']);
        $online_hash = DBEscape($_POST['online_hash']);
        $img_principal = DBEscape($_POST['img_principal']);
        $protect_hash = DBEscape($_POST['protect_hash']);

        if (UsuarioVerificado($id_usuario, $online_hash)) {

            $result = DBRead('tb_suspeitos_imagem', "WHERE id_suspeito = {$id_suspeito} AND img_principal = '$img_principal' LIMIT 1", 'protect_hash, img_busca');

            if (is_array($result)) {

                if ($result[0]['protect_hash'] == $protect_hash || isAdmin($id_usuario)) {

                    $img_busca = $result[0]['img_busca'];

                    DBExecuteMultiQuery(

                        "UPDATE tb_suspeitos_imagem SET item_excluido = 1 WHERE id_suspeito = {$id_suspeito} AND img_principal = '$img_principal' LIMIT 1;\n".
                        "UPDATE tb_sistema SET total_imagens_spt = total_imagens_spt - 2 WHERE id = 1 LIMIT 1;"
                    );

                    $result2 = DBRead('tb_suspeitos_imagem', "WHERE img_principal = '$img_principal' AND item_excluido = 0 LIMIT 1");

                    if (!is_array($result2)) {

                        unlink(GetImagePathInServer($img_principal));
                    }

                    $result2 = DBRead('tb_suspeitos_imagem', "WHERE img_busca = '$img_busca' AND item_excluido = 0 LIMIT 1");

                    if (!is_array($result2)) {

                        unlink(GetImagePathInServer($img_busca));
                    }

                    Sucesso('Imagem excluída.');
                }
                else {

                    Erro('Você não pode excluir uma imagem adicionada por outro usuário.');
                }
            }
            else {

                Erro('Imagem não encontrada.');
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
