<?php

	if (!empty($_POST['cpf']) 			&&
        !empty($_POST['matricula']) 	&&
        !empty($_POST['id_aparelho']) 	&&
        !empty($_POST['instituicao'])) {

		$cpf = DBEscape($_POST['cpf']);
		$matricula = DBEscape($_POST['matricula']);
		$id_aparelho = DBEscape($_POST['id_aparelho']);
		$instituicao = DBEscape($_POST['instituicao']);

		$result = DBRead('tb_usuarios', "WHERE cpf = '$cpf' AND matricula = '$matricula' AND instituicao = '$instituicao' AND conta_excluida = 0 LIMIT 1", 'id_aparelho, email_cadastro');

        if (is_array($result)) {

			if ($result[0]['id_aparelho'] == $id_aparelho) {

				$data = array('id_aparelho' => 'confirmado');

				Sucesso('Informações conferidas.', $data);
			}
			else {

				$result2 = DBRead('tb_recuperar_senha', "WHERE id_aparelho = '$id_aparelho' AND data_registro >= CURDATE()",'count(*) as solicitacoes');

				if (is_array($result2)) {

					if ($result2[0]['solicitacoes'] >= 3) {

						Erro('Você excedeu o número de solicitações permitido por hoje.');
					}
				}

				$email = $result[0]['email_cadastro'];
				$token = GenerateHash();
				$link = 'http://www.sincabs.com.br/app/webservice-v-1.4/recuperar-senha.php?token='.$token.'&data1='.$cpf.'&data2='.$matricula;

				require_once 'sistema/PHPMailer_5.2.4/class.phpmailer.php';

				$mail = new PHPMailer;

				$mail->IsSMTP();
				$mail->Host = ZOHO_SMTP;
				$mail->SMTPAuth = true;
				$mail->Port = 465;
				$mail->Username = ZOHO_USER;
				$mail->Password = ZOHO_PASS;
				$mail->SMTPSecure = 'ssl';
				$mail->CharSet = 'UTF-8';

				$mail->From = ZOHO_USER;
				$mail->FromName = 'Sincabs';
				$mail->AddAddress($email);
				$mail->IsHTML(true);

				$mail->Subject = 'Recuperação de senha';
				$mail->Body    = '<html><body><p><tt><kbd><span style="font-size:18px;"><b>Recuperação de senha</b></br></span></kbd></tt></p><p>Este é um email automático enviado pelo sistema para recuperar sua senha.</p><p>Acesse o link abaixo para confirmar sua solicitação.</p></br><p></p><p><strong><span style="font-size:14px;">Confirmar solicitação: <a href="'.$link.'">'. $link .'</a></span></strong></p></br></body></html>';

				if ($mail->Send()) {

					$agora = date('Y-m-d H:i:s', time());

					$data = array(
						'token' 		=> $token,
						'cpf' 			=> $cpf,
						'matricula' 	=> $matricula,
						'confirmado'	=> 0,
						'id_aparelho'	=> $id_aparelho,
						'data_registro'	=> $agora);

					DBCreate('tb_recuperar_senha', $data);

					Sucesso("Enviamos um e-mail para você.\n\nAcesse sua caixa de entrada e confirme a solicitação para recuperar sua senha.");
				}
				else {

					Erro('Não foi possível solicitar a recuperação da senha devido à conexão com o servidor de e-mail. Tente novamente mais tarde.');
				}
			}
        }
		else {

			Erro('As informações inseridas não pertencem a nenhum usuário.');
		}
	}
	else {

		sincabsDie('Acesso negado.');
	}

?>
