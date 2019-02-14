<?php

	require 'sistema/config.php';
	require 'sistema/connection.php';
	require 'sistema/database.php';
	require 'sistema/utils.php';
	require 'sistema/MCrypt.php';

	if (strpos($_SERVER['HTTP_USER_AGENT'], SYSTEM_NAME) === false) {

		sincabsDie('Acesso negado.');
	}

	date_default_timezone_set('America/Araguaina');

	$mcrypt = new MCrypt();

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

	$is_spt = false;

	$name_parts = explode('-', $_FILES['img_principal']['name']);

	if ($name_parts[0] == 'SPT') {

		$is_spt = true;
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

	if ($img_count > 0) {

		if ($is_spt) {

			DBExecuteNoError("UPDATE tb_sistema SET total_imagens_spt = total_imagens_spt + {$img_count} WHERE id = 1 LIMIT 1");
		}
		else {

			DBExecuteNoError("UPDATE tb_sistema SET total_imagens_usr = total_imagens_usr + {$img_count} WHERE id = 1 LIMIT 1");
		}
	}

	Sucesso('Imagens enviadas.');

?>
