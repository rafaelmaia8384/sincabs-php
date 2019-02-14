<?php

	if (!empty($_POST['id_usuario']) 		&&
        !empty($_POST['area_de_atuacao'])	&&
        !empty($_POST['id_suspeito'])		&&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $id_suspeito = DBEscape($_POST['id_suspeito']);
		$online_hash = DBEscape($_POST['online_hash']);
        $area_de_atuacao = DBEscape($_POST['area_de_atuacao']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            $protect_hash = GetProtectHash($id_usuario);

            $result = DBRead('tb_suspeitos', "WHERE id_suspeito = {$id_suspeito} LIMIT 1", 'protect_hash');

            if (is_array($result)){

                if ($result[0]['protect_hash'] != $protect_hash && !isAdmin($id_usuario)) {

                    sincabsDie('Acesso negado.');
                }

                DBExecute("UPDATE tb_suspeitos SET areas_de_atuacao = {$area_de_atuacao} WHERE id_suspeito = {$id_suspeito} LIMIT 1");

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

					if (($area_de_atuacao & $shift) > 0) {

						$uf_string .= $uf[$b+1];
						$uf_string .= ', ';
					}
				}

				$uf_string = substr($uf_string, 0, -2);

				$result_array = array('area_de_atuacao' => $uf_string);

                Sucesso('Nome/alcunha editado com sucesso.', $result_array);
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
