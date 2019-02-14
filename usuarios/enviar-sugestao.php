<?php

    if (!empty($_POST['id_usuario'])    &&
        !empty($_POST['protect_hash'])  &&
        !empty($_POST['sugestao'])      &&
        !empty($_POST['online_hash'])) {

        $id_usuario = DBEscape($_POST['id_usuario']);
        $sugestao = DBEscape($_POST['sugestao']);
        $protect_hash = DBEscape($_POST['protect_hash']);
        $online_hash = DBEscape($_POST['online_hash']);

        if (UsuarioVerificado($id_usuario, $online_hash)) {

            $result = DBRead('tb_sugestoes', "WHERE id_usuario = {$id_usuario} AND data_registro >= CURDATE()",'count(*) as sugestoes');

            if (is_array($result)) {

                if ($result[0]['sugestoes'] >= 3) {

                    Erro('Aguarde um tempo para enviar uma nova sugestão.');
                }
            }

            $agora = date('Y-m-d H:i:s', time());

            $sugestao_array = array(

                    'id_usuario'    => $id_usuario,
                    'sugestao'      => $sugestao,
                    'protect_hash'  => $protect_hash,
                    'data_registro' => $agora,
                    'item_excluido' => 0
            );

            DBCreate('tb_sugestoes', $sugestao_array);

            Sucesso('Sua sugestão foi enviada. Obrigado pela colaboração!');
        }
        else {

            Erro(ERROR_OFFLINE);
        }
    }
    else {

        sincabsDie('Acesso negado.');
    }

?>
