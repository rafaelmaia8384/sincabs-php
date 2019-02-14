<?php

	if (!empty($_POST['id_suspeito']) 	&&
        !empty($_POST['id_usuario'])    &&
		!empty($_POST['online_hash'])) {

		$id_suspeito = DBEscape($_POST['id_suspeito']);
        $id_usuario = DBEscape($_POST['id_usuario']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

			$result = DBRead('tb_suspeitos', "WHERE suspeito_excluido = 0 AND id_suspeito = {$id_suspeito} LIMIT 1", '*');

			if (is_array($result)) {

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

				$total = count($result);

				for ($a = 0; $a < $total; $a++) {

					$result[$a]['protect_hash'] = '';

					$uf_string = '';

					for ($b = 0; $b < 27; $b++) {

						$shift = 1 << $b;

						if (($result[$a]['areas_de_atuacao'] & $shift) > 0) {

							$uf_string .= $uf[$b+1];
							$uf_string .= ', ';
						}
					}

					$uf_string = substr($uf_string, 0, -2);

					$result[$a]['areas_de_atuacao'] = $uf_string;
				}

				DBExecuteNoError("UPDATE tb_suspeitos SET num_visualizacoes = num_visualizacoes + 1 WHERE id_suspeito = {$id_suspeito} LIMIT 1");

				Sucesso('Perfil encontrado.', $result[0]);
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
