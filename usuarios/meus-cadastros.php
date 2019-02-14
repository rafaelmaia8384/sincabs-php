<?php

	if (!empty($_POST['id_usuario']) 		&&
        !empty($_POST['index'])      		&&
		!empty($_POST['online_hash'])) {

		$id_usuario = DBEscape($_POST['id_usuario']);
        $index = DBEscape($_POST['index']);
		$online_hash = DBEscape($_POST['online_hash']);

		if (UsuarioVerificado($id_usuario, $online_hash)) {

            $search_limit = GetSearchLimit();

            $limit = ($index - 1) * $search_limit;

			$result = DBRead('tb_suspeitos', "WHERE id_usuario = {$id_usuario} AND suspeito_excluido = 0 ORDER BY data_registro DESC LIMIT {$limit}, {$search_limit}", 'img_principal, img_busca, id_suspeito, nome_alcunha, nome_completo, data_registro, areas_de_atuacao');

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

					if (strlen($result[$a]['nome_completo']) > 0 && $result[$a]['nome_completo'] !== $result[$a]['nome_alcunha']) {

						$result[$a]['nome_alcunha'] = $result[$a]['nome_completo'] . " ({$result[$a]['nome_alcunha']})";
					}
				}

				$result_array = array('Resultado' => $result);

				Sucesso('Usuários encontrados.', $result_array);
			}
			else {

				Erro('Você ainda não cadastrou nenhum suspeito na plataforma.');
			}
		}
		else {

			Erro(ERROR_OFFLINE);
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

	function pegarUF($uf) {

		$uf_array = array(

			"1" => "AC",
			"2" => "AL",
			"3" => "AM",
			"4" => "AP",
			"5" => "BA",
			"6" => "CE",
			"7" => "DF",
			"8" => "ES",
			"9" => "GO",
			"10" => "MA",
			"11" => "MG",
			"12" => "MS",
			"13" => "MT",
			"14" => "PA",
			"15" => "PB",
			"16" => "PE",
			"17" => "PI",
			"18" => "PR",
			"19" => "RJ",
			"20" => "RN",
			"21" => "RO",
			"22" => "RR",
			"23" => "RS",
			"24" => "SC",
			"25" => "SE",
			"26" => "SP",
			"27" => "TO"
		);

		return $uf_array[$uf];
	}

?>
