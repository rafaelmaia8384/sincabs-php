<?php

    if (!empty($_POST['id_usuario']) &&             // OK
        !empty($_POST['nome_alcunha']) &&
        !empty($_POST['nome_completo']) &&
        !empty($_POST['nome_da_mae']) &&
        !empty($_POST['cpf']) &&
        !empty($_POST['rg']) &&
        !empty($_POST['data_nascimento']) &&
        !empty($_POST['crt_cor_pele']) &&
        !empty($_POST['crt_cor_olhos']) &&
        !empty($_POST['crt_cor_cabelos']) &&
        !empty($_POST['crt_tipo_cabelos']) &&
        !empty($_POST['crt_porte_fisico']) &&
        !empty($_POST['crt_estatura']) &&
        !empty($_POST['crt_deficiente']) &&
        !empty($_POST['crt_tatuagem']) &&
        !empty($_POST['relato']) &&
        !empty($_POST['historico_criminal']) &&
        !empty($_POST['areas_de_atuacao']) &&
        !empty($_POST['protect_hash']) &&           // OK
        !empty($_POST['online_hash'])) {            // OK

        $id_usuario = DBEscape($_POST['id_usuario']);
        $nome_alcunha = DBEscape($_POST['nome_alcunha']);
        $nome_completo = DBEscape($_POST['nome_completo']);
        $nome_da_mae = DBEscape($_POST['nome_da_mae']);
        $cpf = DBEscape($_POST['cpf']);
        $rg = DBEscape($_POST['rg']);
        $data_nascimento = DBEscape($_POST['data_nascimento']);
        $crt_cor_pele = DBEscape($_POST['crt_cor_pele']);
        $crt_cor_olhos = DBEscape($_POST['crt_cor_olhos']);
        $crt_cor_cabelos = DBEscape($_POST['crt_cor_cabelos']);
        $crt_tipo_cabelos = DBEscape($_POST['crt_tipo_cabelos']);
        $crt_porte_fisico = DBEscape($_POST['crt_porte_fisico']);
        $crt_estatura = DBEscape($_POST['crt_estatura']);
        $crt_deficiente = DBEscape($_POST['crt_deficiente']);
        $crt_tatuagem = DBEscape($_POST['crt_tatuagem']);
        $relato = DBEscape($_POST['relato']);
        $historico_criminal = DBEscape($_POST['historico_criminal']);
        $areas_de_atuacao = DBEscape($_POST['areas_de_atuacao']);
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

            require 'sistema/metaphonePTBR.php';

            $metaphone = new Metaphone();

            $nome_alcunha_soundex = $metaphone->getPhraseMetaphone($nome_alcunha);

            $nome_alcunha = ucwords(strtolower(trim($nome_alcunha)));

            $nome_completo_soundex = '';

            if ($nome_completo != 'null') {

                $nome_completo_soundex = $metaphone->getPhraseMetaphone($nome_completo);

                $nome_completo = ucwords(strtolower(trim($nome_completo)));
            }
            else {

                $nome_completo = '';
            }

            if ($nome_da_mae == 'null') {

                $nome_da_mae = '';
            }

            if ($cpf == 'null') {

                $cpf = '0';
            }

            if ($rg == 'null') {

                $rg = '0';
            }

            if ($data_nascimento == 'null') {

                $data_nascimento = '0000-00-00';
            }

            $agora = date('Y-m-d H:i:s', time());

            $id_suspeito = '';

            do {

                $id_suspeito = GenerateId();

                $result = DBRead('tb_suspeitos', "WHERE id_suspeito = {$id_suspeito} LIMIT 1", 'id');
            }
            while (is_array($result));

            $suspeito = array(

                'id_usuario'				=> $id_usuario,
                'id_suspeito'               => $id_suspeito,
                'img_principal' 			=> $principal_name,
                'img_busca'					=> $busca_name,

                'nome_alcunha'              => $nome_alcunha,
                'nome_alcunha_soundex'      => $nome_alcunha_soundex,
                'nome_completo'             => $nome_completo,
                'nome_completo_soundex'     => $nome_completo_soundex,
                'nome_da_mae'               => $nome_da_mae,
                'cpf'                       => $cpf,
                'rg'                        => $rg,
                'data_nascimento'           => $data_nascimento,

                'historico_criminal'        => $historico_criminal,
                'areas_de_atuacao'          => $areas_de_atuacao,

                'crt_cor_pele'              => $crt_cor_pele,
                'crt_cor_olhos'             => $crt_cor_olhos,
                'crt_cor_cabelos'           => $crt_cor_cabelos,
                'crt_tipo_cabelos'          => $crt_tipo_cabelos,
                'crt_porte_fisico'          => $crt_porte_fisico,
                'crt_estatura'              => $crt_estatura,
                'crt_possui_deficiencia'    => $crt_deficiente,
                'crt_possui_tatuagem'       => $crt_tatuagem,

                'txt_relato'                => $relato,

                'num_visualizacoes'         => 0,

                'data_registro'             => $agora,
                'suspeito_excluido'         => 0,
                'protect_hash'              => $protect_hash
            );

            DBCreate('tb_suspeitos', $suspeito);

            DBExecuteMultiQuery(

                "UPDATE tb_usuarios SET num_suspeitos = num_suspeitos + 1 WHERE id_usuario = {$id_usuario} LIMIT 1;\n".
                "UPDATE tb_sistema SET total_imagens_spt = total_imagens_spt + {$img_count}, total_suspeitos = total_suspeitos + 1 WHERE id = 1 LIMIT 1;\n"
            );

            $uf = array(

                '1' => 'AC',
                '2' => 'AL',
                '3' => 'AM',
                '4' => 'AP',
                '5' => 'BA',
                '6' => 'CE',
                '7' => 'DF',
                '8' => 'ES',
                '9' => 'GO',
                '10' => 'MA',
                '11' => 'MG',
                '12' => 'MS',
                '13' => 'MT',
                '14' => 'PA',
                '15' => 'PB',
                '16' => 'PE',
                '17' => 'PI',
                '18' => 'PR',
                '19' => 'RJ',
                '20' => 'RN',
                '21' => 'RO',
                '22' => 'RR',
                '23' => 'RS',
                '24' => 'SC',
                '25' => 'SE',
                '26' => 'SP',
                '27' => 'TO'
            );

            $uf_string = '';

            for ($b = 0; $b < 27; $b++) {

                $shift = 1 << $b;

                if (($suspeito['areas_de_atuacao'] & $shift) > 0) {

                    $uf_string .= $uf[$b+1];
                    $uf_string .= ', ';
                }
            }

            $uf_string = substr($uf_string, 0, -2);

            $suspeito['areas_de_atuacao'] = $uf_string;

        	Sucesso('Suspeito cadastrado.', $suspeito);
        }
        else {

            Erro(ERROR_OFFLINE);
        }
    }
    else {

        sincabsDie('Acesso negado.');
    }

?>
