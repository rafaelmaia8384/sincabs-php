<?php

    if (!empty($_POST['id_usuario']) &&
        !empty($_POST['protect_hash']) &&
        !empty($_POST['online_hash'])) {

        $id_usuario = DBEscape($_POST['id_usuario']);
        $online_hash = DBEscape($_POST['online_hash']);
        $protect_hash = DBEscape($_POST['protect_hash']);

        if (UsuarioVerificado($id_usuario, $online_hash)) {

            $img_principal = null;
        	$img_busca = null;

            if (!empty($_FILES['img_principal'])) {

        		if ($_FILES['img_principal']['error'] != 0) {

        			Erro('Erro no envio da imagem. Tente novamente mais tarde.');
        		}

        		if (($_FILES['img_principal']['size'] / 1024) > MAX_FILE_SIZE_UPLOAD) {

        			Erro('O tamanho máximo da imagem deve ser de '. MAX_FILE_SIZE_UPLOAD .'kB.');
        		}

        		$img_principal = GetImagePathInServer($_FILES['img_principal']['name']);
        	}
        	else {

        		Erro('Arquivo não anexado.');
        	}

            if (!empty($_FILES['img_busca'])) {

        		if ($_FILES['img_busca']['error'] != 0) {

        			Erro('Erro no envio da imagem. Tente novamente mais tarde.');
        		}

        		if (($_FILES['img_busca']['size'] / 1024) > MAX_FILE_SIZE_UPLOAD) {

        			Erro('O tamanho máximo da imagem deve ser de '. MAX_FILE_SIZE_UPLOAD .'kB.');
        		}

        		$img_busca = GetImagePathInServer($_FILES['img_busca']['name']);
        	}
        	else {

        		Erro('Arquivo não anexado.');
        	}

            if ($img_principal != null && !file_exists($img_principal)) {

                if (!file_exists(dirname($img_principal))) {

                    if (!mkdir(dirname($img_principal), 0755, true)) {

                        Erro('O arquivo não foi salvo no servidor. Tente novamente mais tarde.');
                    }
                }

                if (!move_uploaded_file($_FILES['img_principal']['tmp_name'], $img_principal)) {

                    Erro('O arquivo não foi salvo no servidor. Tente novamente mais tarde.');
                }
            }

            if ($img_busca != null && !file_exists($img_busca)) {

                if (!file_exists(dirname($img_busca))) {

                    if (!mkdir(dirname($img_busca), 0755, true)) {

                        Erro('O arquivo não foi salvo no servidor. Tente novamente mais tarde.');
                    }
                }

                if (!move_uploaded_file($_FILES['img_busca']['tmp_name'], $img_busca)) {

                    Erro('O arquivo não foi salvo no servidor. Tente novamente mais tarde.');
                }
            }

            $principal_name = DBEscape($_FILES['img_principal']['name']);
            $busca_name = DBEscape($_FILES['img_busca']['name']);

            $old_principal = null;
            $old_busca = null;

            $result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario} AND conta_excluida = 0 LIMIT 1", 'img_principal, img_busca, protect_hash');

            if (is_array($result)) {

                if ($result[0]['protect_hash'] == $protect_hash) {

                    $old_principal = $result[0]['img_principal'];
                    $old_busca = $result[0]['img_busca'];

                    DBExecute("UPDATE tb_usuarios SET img_principal = '$principal_name', img_busca = '$busca_name' WHERE id_usuario = {$id_usuario} LIMIT 1");

                    $result2 = DBRead('tb_usuarios', "WHERE img_principal = '$old_principal' AND conta_excluida = 0 LIMIT 1");

                    if (!is_array($result2)) {

                        unlink(GetImagePathInServer($old_principal));
                    }

                    $result2 = DBRead('tb_usuarios', "WHERE img_busca = '$old_busca' AND conta_excluida = 0 LIMIT 1");

                    if (!is_array($result2)) {

                        unlink(GetImagePathInServer($old_busca));
                    }

                    $img_array = array(

                            'img_principal' => $principal_name,
                            'img_busca'     => $busca_name
                    );

                	Sucesso('Sua imagem de perfil foi alterada.', $img_array);
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
