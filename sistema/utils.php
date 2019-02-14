<?php

	function dieCryptHex($jsonCode) {

		header('Content-Type: text/plain; charset=utf-8');

		die($GLOBALS['mcrypt']->encrypt(base64_encode($jsonCode)));
	}

	function Sucesso($mensagem, array &$extra = null) {

		DBClose();

		$response = array('Erro' => '0', 'Mensagem' => $mensagem, 'Extra' => $extra);

		dieCryptHex(json_encode($response, JSON_UNESCAPED_UNICODE));
	}

	function Erro($mensagem) {

		DBClose();

		$response = array('Erro' => '1', 'Mensagem' => $mensagem, 'Extra' => null);

		dieCryptHex(json_encode($response, JSON_UNESCAPED_UNICODE));
	}

	function Fatal() {

		$link = DBConnect();

		$erro = mysqli_error($link);
		$hora = date("Y-m-d H:i:s", time());

		DBClose();

		file_put_contents(MYSQL_ERROR_LOG, 'Erro: '. $erro . "\r\n" . 'Hora: '. $hora . "\r\n\r\n", FILE_APPEND);

		$response = array('Erro' => '2', 'Mensagem' => 'Ocorreu uma falha no sistema. Tente novamente dentro de alguns minutos.', 'Extra' => null);

		dieCryptHex(json_encode($response, JSON_UNESCAPED_UNICODE));
	}

	function GenerateId() {

		return hexdec(GenerateHexNumberId());
	}

	function GenerateHexNumberId() {

	    $characters = '0123456789ABCDEF';
    	$charactersLength = strlen($characters);
    	$randomString = '';

    	for ($i = 0; $i < LENGTH_ID; $i++) {

        	if ($i == 0) $randomString .= $characters[rand(1, $charactersLength - 1)];
			else $randomString .= $characters[rand(0, $charactersLength - 1)];
    	}

    	return $randomString;
	}

	function GenerateHash() {

	    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    	$charactersLength = strlen($characters);
    	$randomString = '';

    	for ($i = 0; $i < LENGTH_HASH; $i++) {

        	$randomString .= $characters[rand(0, $charactersLength - 1)];
    	}

    	return $randomString;
	}

	function ValidaCPF($cpf) {

	    $cpf = preg_replace('/[^0-9]/', '', $cpf);

	    if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {

			return false;
	    }
		else {

	        for ($t = 9; $t < 11; $t++) {

			    for ($d = 0, $c = 0; $c < $t; $c++) {

	                $d += $cpf{$c} * (($t + 1) - $c);
	            }

	            $d = ((10 * $d) % 11) % 10;

	            if ($cpf{$c} != $d) {

	                return false;
	            }
	        }

	        return true;
	    }
	}

	function ValidaEmail($email) {

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

 		   return false;
		}

		return true;
	}

	function ValidaSenha($senha) {

		$len = strlen($senha);

		if ($len < 6 || $len > 20) return false;

		return true;
	}

	function ValidaCadastroCPF($cpf) {

		$result = DBRead('tb_usuarios', "WHERE cpf = '$cpf' AND conta_excluida = 0 LIMIT 1", 'id');

		return !is_array($result);
	}

	function GetSearchLimit() {

		$result = DBRead('tb_sistema', "WHERE id = 1 LIMIT 1", 'search_limit');

		if (is_array($result)) {

			return $result[0]['search_limit'];
		}
		else {

			return 20;
		}
	}

	function GetProtectHash($id_usuario) {

		$result = DBRead('tb_usuarios', "WHERE id_usuario = '$id_usuario' LIMIT 1", 'protect_hash');

		if (is_array($result)) {

			return $result[0]['protect_hash'];
		}
		else {

			return null;
		}
	}

	function UsuarioVerificado($id_usuario, $online_hash) {

		$result = DBRead('tb_usuarios', "WHERE id_usuario = '$id_usuario' AND conta_excluida = 0 LIMIT 1", 'online_hash, conta_bloqueada, motivo_bloqueio, instituicao');

		if (is_array($result)) {

			if ($result[0]['conta_bloqueada'] == 1) {

				Erro("Sua conta está temporariamente bloqueada.\n\nMotivo:\n\n".$result[0]['motivo_bloqueio']);
			}

			if ($result[0]['online_hash'] == $online_hash) {

				return $result[0]['instituicao'];
			}
		}

		return 0;
	}

	function GetImagePathInServer($img_name) {

		if (strlen($img_name) < 58 || strlen($img_name) > 62) {

			return null;
		}

		$img_parts = explode('-', $img_name);

		if (count($img_parts) != 5) {

			return null;
		}

		if (strlen($img_parts[0]) != 3 || strlen($img_parts[1]) != 1 || strlen($img_parts[2]) < 3 || strlen($img_parts[3]) < 4 || strlen($img_parts[4]) != 44) {

			return null;
		}

		$extensions = array('jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG');

		$ext = explode('.', $img_parts[4]);

		if (!in_array($ext[1], $extensions)) {

			return null;
		}

		$principal_busca = ($img_parts[1] == 'P' ? 'principal' : 'busca');
		$folder_by_size = GetFolderBySize($img_parts[3]);

		if ($folder_by_size == null) {

			return null;
		}

		return '../data/' . strtolower($img_parts[0]) . '/' . $principal_busca . '/' . $img_parts[2] . '/' . $folder_by_size . '/' . $img_parts[4][0] . '/' . $img_parts[4][1] . '/' . $img_parts[4][2] . '/' . $img_parts[4][3] . '/' . $img_name;
	}

	function GetFolderBySize($size) {

		if (!preg_match("/^[0-9]+$/", $size)) {

			return null;
		}

		if ($size < 5120) {

			return 'a5kB';
		}
		else if ($size < 10240) {

			return 'b10kB';
		}
		else if ($size < 15360) {

			return 'c15kB';
		}
		else if ($size < 20480) {

			return 'd20kB';
		}
		else if ($size < 30720) {

			return 'e30kB';
		}
		else if ($size < 40960) {

			return 'f40kB';
		}
		else if ($size < 51200) {

			return 'g50kB';
		}
		else if ($size < 76800) {

			return 'h75kB';
		}
		else if ($size < 102400) {

			return 'i100kB';
		}
		else if ($size < 128000) {

			return 'j125kB';
		}
		else if ($size < 153600) {

			return 'k150kB';
		}
		else if ($size < 179200) {

			return 'l175kB';
		}
		else if ($size < 204800) {

			return 'm200kB';
		}
		else if ($size < 256000) {

			return 'n250kB';
		}
		else if ($size < 307200) {

			return 'o300kB';
		}
		else if ($size < 358400) {

			return 'p350kB';
		}
		else if ($size < 409600) {

			return 'q400kB';
		}
		else if ($size < 460800) {

			return 'r450kB';
		}
		else if ($size < 512000) {

			return 's500kB';
		}
		else if ($size < 768000) {

			return 't750kB';
		}
		else if ($size < 1024000) {

			return 'u1MB';
		}
		else {

			return 'v1MBover';
		}
	}

?>
