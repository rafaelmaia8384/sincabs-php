<?php

    if (!empty($_POST['id_usuario']) &&
        !empty($_POST['online_hash'])) {

        $id_usuario = DBEscape($_POST['id_usuario']);
        $online_hash = DBEscape($_POST['online_hash']);

        if (UsuarioVerificado($id_usuario, $online_hash)) {

            $img_id_verso_principal = null;
            $img_id_verso_busca = null;

            if (!empty($_FILES['img_id_verso_principal'])) {

        		if ($_FILES['img_id_verso_principal']['error'] != 0) {

        			Erro('Erro no envio da imagem. Tente novamente mais tarde.');
        		}

        		if (($_FILES['img_id_verso_principal']['size'] / 1024) > MAX_FILE_SIZE_UPLOAD) {

        			Erro('O tamanho máximo da imagem deve ser de '. MAX_FILE_SIZE_UPLOAD .'kB.');
        		}

        		$img_id_verso_principal = GetImagePathInServer($_FILES['img_id_verso_principal']['name']);
        	}
        	else {

        		Erro('Arquivo não anexado.');
        	}

            if (!empty($_FILES['img_id_verso_busca'])) {

        		if ($_FILES['img_id_verso_busca']['error'] != 0) {

        			Erro('Erro no envio da imagem. Tente novamente mais tarde.');
        		}

        		if (($_FILES['img_id_verso_busca']['size'] / 1024) > MAX_FILE_SIZE_UPLOAD) {

        			Erro('O tamanho máximo da imagem deve ser de '. MAX_FILE_SIZE_UPLOAD .'kB.');
        		}

        		$img_id_verso_busca = GetImagePathInServer($_FILES['img_id_verso_busca']['name']);
        	}
        	else {

        		Erro('Arquivo não anexado.');
        	}

            $img_count = 0;

            if ($img_id_verso_principal != null && !file_exists($img_id_verso_principal)) {

                $img_count++;

                if (!file_exists(dirname($img_id_verso_principal))) {

                    if (!mkdir(dirname($img_id_verso_principal), 0755, true)) {

                        Erro('O arquivo não foi salvo no servidor. Tente novamente mais tarde.');
                    }
                }

                if (!move_uploaded_file($_FILES['img_id_verso_principal']['tmp_name'], $img_id_verso_principal)) {

                    Erro('O arquivo não foi salvo no servidor. Tente novamente mais tarde.');
                }
            }

            if ($img_id_verso_busca != null && !file_exists($img_id_verso_busca)) {

                $img_count++;

                if (!file_exists(dirname($img_id_verso_busca))) {

                    if (!mkdir(dirname($img_id_verso_busca), 0755, true)) {

                        Erro('O arquivo não foi salvo no servidor. Tente novamente mais tarde.');
                    }
                }

                if (!move_uploaded_file($_FILES['img_id_verso_busca']['tmp_name'], $img_id_verso_busca)) {

                    Erro('O arquivo não foi salvo no servidor. Tente novamente mais tarde.');
                }
            }

            $verso_principal_name = DBEscape($_FILES['img_id_verso_principal']['name']);
            $verso_busca_name = DBEscape($_FILES['img_id_verso_busca']['name']);

            DBExecute("UPDATE tb_usuarios SET img_id_verso_principal = '$verso_principal_name', img_id_verso_busca = '$verso_busca_name' WHERE id_usuario = '$id_usuario' AND conta_excluida = 0 LIMIT 1");

            if ($img_count > 0) {

        		DBExecuteNoError("UPDATE tb_sistema SET total_imagens_usr = total_imagens_usr + {$img_count} WHERE id = 1 LIMIT 1");
        	}

        	Sucesso("Documentos enviados.\n\nSeu cadastro será analisado para que seu acesso à plataforma seja liberado.");
        }
        else {

            Erro(ERROR_OFFLINE);
        }
    }
    else {

        sincabsDie('Acesso negado.');
    }

?>
