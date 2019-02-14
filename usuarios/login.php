<?php

	define('DIAS_BLOQUEIO', 90); //90 dias de bloqueio caso o usuário receba muitas denúncias

	if (!empty($_POST['cpf']) 		&&
		!empty($_POST['senha']) 	&&
		!empty($_POST['id_aparelho'])) {

		/*require 'sistema/metaphonePTBR.php';

		$metaphone = new Metaphone();

		for ($i = 1; $i < 90; $i++) {

			$result = DBRead('tb_usuarios', "WHERE id = {$i} LIMIT 1", 'nome_completo');

			$nome_completo_soundex = $metaphone->getPhraseMetaphone($result[0]['nome_completo']);

            DBExecute("UPDATE tb_usuarios SET nome_completo_soundex = '$nome_completo_soundex' WHERE id = {$i}");
		}

		Erro('Concluído.');*/

		$cpf = DBEscape($_POST['cpf']);
		$senha = DBEscape($_POST['senha']);
		$id_aparelho = DBEscape($_POST['id_aparelho']);

		if (!ValidaCPF($cpf)) {

			Erro(ERROR_WRONG_PASSWORD); //para confundir o hacker
		}
		elseif (!ValidaSenha($senha)) {

			Erro(ERROR_WRONG_PASSWORD); //para confundir o hacker
		}

		$senha = hash('sha512', $senha);

		$result = DBRead('tb_usuarios', "WHERE cpf = '$cpf' AND senha = '$senha' AND conta_excluida = 0 ORDER BY data_registro DESC LIMIT 1", '*');

		if (is_array($result)) {

			$online_hash = GenerateHash();

			$id_usuario = $result[0]['id_usuario'];
			$ultima_atividade = $result[0]['ultima_atividade'];

			$conta_bloqueada = $result[0]['conta_bloqueada'];
			$motivo_bloqueio = $result[0]['motivo_bloqueio'];

			$result[0]['online_hash'] = $online_hash;

			if ($result[0]['id_aparelho'] == $id_aparelho) {

				if ($conta_bloqueada == 0) {

					Login($id_usuario, $online_hash, $result[0]);
				}
				else {

					if (strlen($motivo_bloqueio) > 0) {

						Erro("Acesso negado. Seu acesso à plataforma está bloqueado.\n\nMotivo:\n\n".$motivo_bloqueio);
					}
					else {

						$result2 = DBRead('tb_usuarios_denuncia', "WHERE id_usuario_denunciado = {$id_usuario} AND item_excluido = 0 ORDER BY data_registro DESC LIMIT 1", 'data_registro');

						if (is_array($result2)) {

							$data1 = new DateTime($result2[0]['data_registro']);
							$data2 = new DateTime(date('Y-m-d H:i:s', time()));

							$intervalo = $data1->diff($data2);
							$dias = $intervalo->days;

							$resto = DIAS_BLOQUEIO - $dias;

							if ($dias < DIAS_BLOQUEIO) {

								Erro("Acesso negado.\n\nSeu acesso à plataforma está bloqueado devido às denúncias feitas por outros usuários em seu perfil.\n\nResta(m): {$resto} dia(s).");
							}
							else {

								DBExecuteMultiQuery(

									"UPDATE tb_usuarios SET conta_bloqueada = 0, motivo_bloqueio = '' WHERE id_usuario = {$id_usuario} LIMIT 1;\n".
									"UPDATE tb_usuarios_denuncia SET item_excluido = 1 WHERE id_usuario_denunciado = {$id_usuario};"
								);

								Login($id_usuario, $online_hash, $result[0]);
							}
						}
						else {

							Erro("Acesso negado.\n\nSeu acesso à plataforma está bloqueado durante ".DIAS_BLOQUEIO." dias devido às denúncias feitas por outros usuários em seu perfil.");
						}
					}
				}
			}
			else {

				$data1 = new DateTime($ultima_atividade);
				$data2 = new DateTime(date('Y-m-d H:i:s', time()));

				$intervalo = $data1->diff($data2);
				$horas = ($intervalo->days * 24) + $intervalo->h;

				if ($horas < MIN_WAIT_DIFF_DEVICE) {

					Erro("Você está tentando acessar a plataforma de um aparelho diferente.\n\nAguarde, pelo menos, ".MIN_WAIT_DIFF_DEVICE." horas para acessar a plataforma usando o aparelho atual.\n\nResta(m): ".(MIN_WAIT_DIFF_DEVICE - $horas).' hora(s).');
				}
				else {

					DBExecuteNoError("UPDATE tb_usuarios SET id_aparelho = '$id_aparelho' WHERE id_usuario = '$id_usuario' AND cpf = '$cpf' LIMIT 1");

					Login($id_usuario, $online_hash, $result[0]);
				}
			}
		}
		else {

			Erro(ERROR_WRONG_PASSWORD); // para confundir o hacker
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

	function Login($id_usuario, $online_hash, array &$dados) {

		$ip = getip();

		$result = DBRead('tb_usuarios_ip', "WHERE id_usuario = {$id_usuario} LIMIT 1", 'id');

		if (is_array($result)) {

			DBExecute("UPDATE tb_usuarios_ip SET ultimo_ip = '$ip', data_registro = NOW() WHERE id_usuario = {$id_usuario} LIMIT 1");
		}
		else {

			DBExecute("INSERT INTO tb_usuarios_ip (id_usuario, ultimo_ip, data_registro) VALUES ({$id_usuario}, '$ip', NOW())");
		}

		DBExecute("UPDATE tb_usuarios SET total_logins = total_logins + 1, online_hash = '$online_hash', ultima_atividade = NOW() WHERE id_usuario = '$id_usuario' LIMIT 1");
		DBExecuteNoError('UPDATE tb_sistema SET total_acessos = total_acessos + 1 WHERE id = 1');

		Sucesso('Login efetuado.', $dados);
	}

	function validip($ip) {

		if (!empty($ip) && ip2long($ip) != -1) {

	    $reserved_ips = array (

	    	array('0.0.0.0','2.255.255.255'),
	       	array('10.0.0.0','10.255.255.255'),
	       	array('127.0.0.0','127.255.255.255'),
	       	array('169.254.0.0','169.254.255.255'),
	       	array('172.16.0.0','172.31.255.255'),
	       	array('192.0.2.0','192.0.2.255'),
	       	array('192.168.0.0','192.168.255.255'),
	       	array('255.255.255.0','255.255.255.255')
	    );

	    foreach ($reserved_ips as $r) {

	        $min = ip2long($r[0]);
	        $max = ip2long($r[1]);

			if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) {

				return false;
			}
	    }

		return true;
	   }
	   else {

	       return false;
	   }
	}

	function getip() {

   		if (validip($_SERVER["HTTP_CLIENT_IP"])) {

			return $_SERVER["HTTP_CLIENT_IP"];
   		}

   		foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {

       		if (validip(trim($ip))) {

           		return $ip;
       		}
   		}

   		if (validip($_SERVER["HTTP_PC_REMOTE_ADDR"])) {

        	return $_SERVER["HTTP_PC_REMOTE_ADDR"];
   		}
		elseif (validip($_SERVER["HTTP_X_FORWARDED"])) {

       		return $_SERVER["HTTP_X_FORWARDED"];
   		}
		elseif (validip($_SERVER["HTTP_FORWARDED_FOR"])) {

       		return $_SERVER["HTTP_FORWARDED_FOR"];
   		}
		elseif (validip($_SERVER["HTTP_FORWARDED"])) {

       		return $_SERVER["HTTP_FORWARDED"];
   		}
		else {

       		return $_SERVER["REMOTE_ADDR"];
   		}
	}

?>
