<?php

    if (!empty($_POST['id_usuario']) &&
        !empty($_POST['online_hash'])) {

        $id_usuario = DBEscape($_POST['id_usuario']);
        $online_hash = DBEscape($_POST['online_hash']);

        if (UsuarioVerificado($id_usuario, $online_hash)) {

            $img_id_frente_principal = null;
        	$img_id_frente_busca = null;

            if (!empty($_FILES['img_id_frente_principal'])) {

        		if ($_FILES['img_id_frente_principal']['error'] != 0) {

        			Erro('Erro no envio da imagem. Tente novamente mais tarde.');
        		}

        		if (($_FILES['img_id_frente_principal']['size'] / 1024) > MAX_FILE_SIZE_UPLOAD) {

        			Erro('O tamanho máximo da imagem deve ser de '. MAX_FILE_SIZE_UPLOAD .'kB.');
        		}

        		$img_id_frente_principal = GetImagePathInServer($_FILES['img_id_frente_principal']['name']);
        	}
        	else {

        		Erro('Arquivo não anexado.');
        	}

            if (!empty($_FILES['img_id_frente_busca'])) {

        		if ($_FILES['img_id_frente_busca']['error'] != 0) {

        			Erro('Erro no envio da imagem. Tente novamente mais tarde.');
        		}

        		if (($_FILES['img_id_frente_busca']['size'] / 1024) > MAX_FILE_SIZE_UPLOAD) {

        			Erro('O tamanho máximo da imagem deve ser de '. MAX_FILE_SIZE_UPLOAD .'kB.');
        		}

        		$img_id_frente_busca = GetImagePathInServer($_FILES['img_id_frente_busca']['name']);
        	}
        	else {

        		Erro('Arquivo não anexado.');
        	}

            $img_count = 0;

            if ($img_id_frente_principal != null && !file_exists($img_id_frente_principal)) {

                $img_count++;

                if (!file_exists(dirname($img_id_frente_principal))) {

                    if (!mkdir(dirname($img_id_frente_principal), 0755, true)) {

                        Erro('O arquivo não foi salvo no servidor. Tente novamente mais tarde.');
                    }
                }

                if (!move_uploaded_file($_FILES['img_id_frente_principal']['tmp_name'], $img_id_frente_principal)) {

                    Erro('O arquivo não foi salvo no servidor. Tente novamente mais tarde.');
                }
            }

            if ($img_id_frente_busca != null && !file_exists($img_id_frente_busca)) {

                $img_count++;

                if (!file_exists(dirname($img_id_frente_busca))) {

                    if (!mkdir(dirname($img_id_frente_busca), 0755, true)) {

                        Erro('O arquivo não foi salvo no servidor. Tente novamente mais tarde.');
                    }
                }

                if (!move_uploaded_file($_FILES['img_id_frente_busca']['tmp_name'], $img_id_frente_busca)) {

                    Erro('O arquivo não foi salvo no servidor. Tente novamente mais tarde.');
                }
            }

            $frente_principal_name = DBEscape($_FILES['img_id_frente_principal']['name']);
            $frente_busca_name = DBEscape($_FILES['img_id_frente_busca']['name']);

            DBExecute("UPDATE tb_usuarios SET img_id_frente_principal = '$frente_principal_name', img_id_frente_busca = '$frente_busca_name' WHERE id_usuario = '$id_usuario' AND conta_excluida = 0 LIMIT 1");

            if ($img_count > 0) {

        		DBExecuteNoError("UPDATE tb_sistema SET total_imagens_usr = total_imagens_usr + {$img_count} WHERE id = 1 LIMIT 1");
        	}

        	Sucesso('Imagem enviada.');
        }
        else {

            Erro(ERROR_OFFLINE);
        }
    }
    else {

        sincabsDie('Acesso negado.');
    }

?>
