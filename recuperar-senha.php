<?php

	$useragent = $_SERVER['HTTP_USER_AGENT'];

	if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) ||
		preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {

		require 'sistema/config.php';
    	require 'sistema/connection.php';
    	require 'sistema/database.php';

    	date_default_timezone_set('America/Araguaina');

        if (!empty($_GET['token']) && !empty($_GET['data1']) && !empty($_GET['data2'])) {

			$token = DBEscape($_GET['token']);
        	$cpf = DBEscape($_GET['data1']);
        	$matricula = DBEscape($_GET['data2']);

			$result = DBRead('tb_recuperar_senha', "WHERE token = '$token' AND cpf = '$cpf' AND matricula = '$matricula' LIMIT 1", 'confirmado, data_registro');

	        if (is_array($result)) {

	            if ($result[0]['confirmado'] == '0') {

					$data_registro = $result[0]['data_registro'];

					$data1 = new DateTime($data_registro);
					$data2 = new DateTime(date('Y-m-d H:i:s', time()));

					$intervalo = $data1->diff($data2);

					$minutos = $intervalo->days * 24 * 60;
					$minutos += $intervalo->h * 60;
					$minutos += $intervalo->i;

					if ($minutos >= 30) {

						print file_get_contents('../../content/recuperar-senha/error-link-expirado.html');

						sincabsDie();
					}

	                $senha = mt_rand(100000, 999999);
	                $senha_hash = hash('sha512', $senha);

	                DBExecute("UPDATE tb_usuarios SET senha = '$senha_hash' WHERE cpf = '$cpf' AND matricula = '$matricula' LIMIT 1");
	                DBExecute("UPDATE tb_recuperar_senha SET confirmado = 1 WHERE token = '$token' LIMIT 1");

					$html = '<!doctype html> <html> <head> <meta charset="UTF-8"> <title>SINCABS</title> <style> body { margin: 0; text-align: center; background-color : #eeeeee; } body { margin: 0; text-align: center; background-color : #eeeeee; } div.box1 { width: 100%; height: 30vh; align-items: flex-end; display: flex; background-color : #a7c0cd; } div.container { width: 100%; } img { height: 15vh; } div.msgbox { height: 30vh; margin-top: 15vh; margin-left: 10px; margin-right: 10px; } b { color: #4b636e; font-size: 5vmin; } p.pass { color: #a7c0cd; font-size: 10vmin; } p.aviso { color: #4b636e; font-size: 3vmin; } </style> </head> <body> <div class="box1"><div class="container"><img src="../../content/recuperar-senha/sincabs.png" alt="" /></div></div> <div class="msgbox"><b>Sua nova senha é:</b><p class="pass">';

					$html .= $senha;

					$html .= '</p><p class="aviso">Para sua segurança, acesse a plataforma e modifique sua senha.</p></div> </body> </html>';

					print($html);

	                sincabsDie();
	            }
	            else {

					print file_get_contents('../../content/recuperar-senha/error-link-expirado.html');

					sincabsDie();
	            }
	        }
	        else {

	            sincabsDie('Acesso negado.');
	        }
        }
        else {

            sincabsDie('Acesso negado.');
        }
	}
	else {

		print file_get_contents('../../content/recuperar-senha/error-desktop.html');

		sincabsDie();
	}
?>
