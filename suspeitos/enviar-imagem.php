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

            $img_count = 0;

            if ($img_principal != null && !file_exists($img_principal)) {

                $img_count++;

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

                $img_count++;

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

            $agora = date('Y-m-d H:i:s', time());

            $imagem = array(

                'id_usuario'				=> $id_usuario,
                'id_suspeito'               => $id_suspeito,

                'img_principal' 			=> $principal_name,
                'img_busca'					=> $busca_name,

                'imagem_revisada'           => 0,

                'data_registro'             => $agora,

                'protect_hash'              => $protect_hash,

                'item_excluido'             => 0
            );

            DBCreate('tb_suspeitos_imagem', $imagem);

            if ($img_count > 0) {

        		DBExecuteNoError("UPDATE tb_sistema SET total_imagens_spt = total_imagens_spt + {$img_count} WHERE id = 1 LIMIT 1");
        	}

        	Sucesso('Imagem enviada.', $imagem);
        }
        else {

            Erro(ERROR_OFFLINE);
        }
    }
    else {

        sincabsDie('Acesso negado.');
    }

?>
