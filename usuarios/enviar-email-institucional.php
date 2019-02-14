<?php

    if (!empty($_POST['id_usuario']) &&
        !empty($_POST['email_institucional']) &&
        !empty($_POST['online_hash'])) {

        $id_usuario = DBEscape($_POST['id_usuario']);
        $email_institucional = DBEscape($_POST['email_institucional']);
        $online_hash = DBEscape($_POST['online_hash']);

        if (UsuarioVerificado($id_usuario, $online_hash)) {

        	if (!ValidaEmail($email_institucional)) {

                sincabsDie('Acesso negado.');
            }

            $result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario} LIMIT 1", 'instituicao, uf');

            $instituicao = $result[0]['instituicao'];
            $uf = $result[0]['uf'];

            $uf_array = array(

                '1' => 'AC', '2' => 'AL', '3' => 'AM',
                '4' => 'AP', '5' => 'BA', '6' => 'CE', '7' => 'DF', '8' => 'ES', '9' => 'GO',
                '10' => 'MA', '11' => 'MG', '12' => 'MS', '13' => 'MT', '14' => 'PA', '15' => 'PB',
                '16' => 'PE', '17' => 'PI', '18' => 'PR', '19' => 'RJ', '20' => 'RN', '21' => 'RO',
                '22' => 'RR', '23' => 'RS', '24' => 'SC', '25' => 'SE', '26' => 'SP', '27' => 'TO'
            );

            $email_check1 = '@';
            $email_check2 = '@';
            $email_check3 = '@';
            $email_check4 = '@';
            $email_check5 = '@';
            $email_check6 = '@';
            $email_check7 = '@';
            $email_check8 = '@';
            $email_check9 = '@';
            $email_check10 = '@';
            $email_check11 = '@';

            if ($instituicao == 1) {

                $email_check1 = 'pc.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check2 = 'policiacivil.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check3 = 'pc' . strtolower($uf_array[$uf]) . '.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check4 = 'policiacivil' . strtolower($uf_array[$uf]) . '.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check5 = 'pce' . strtolower($uf_array[$uf]) . '.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check6 = 'ssp.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check7 = 'spds.' . strtolower($uf_array[$uf]) . '.gov.br';
            }
            elseif ($instituicao == 2) {

                $email_check1 = 'pm.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check2 = 'policiamilitar.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check3 = 'bm.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check4 = 'pm' . strtolower($uf_array[$uf]) . '.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check5 = 'pme' . strtolower($uf_array[$uf]) . '.' . strtolower($uf_array[$uf]) . '.gov.br';
            }
            elseif ($instituicao == 3) {

                $email_check1 = 'pf.gov.br';
            }
            elseif ($instituicao == 4) {

                $email_check1 = 'prf.gov.br';
            }
            elseif ($instituicao == 5) {

                Erro("Devido à falta de informações sobre os e-mails institucionais penitenciários estaduais, você deve comprovar sua identidade através do envio do documento funcional no passo anterior.");
            }
            elseif ($instituicao == 6) {

                $email_check1 = 'mj.gov.br';
                $email_check1 = 'depen.gov.br';
                $email_check1 = 'depen.' . strtolower($uf_array[$uf]) . '.gov.br';
            }
            elseif ($instituicao == 7) {

                $email_check1 = 'pm.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check2 = 'policiamilitar.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check3 = 'bm.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check4 = 'pm' . strtolower($uf_array[$uf]) . '.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check5 = 'pme' . strtolower($uf_array[$uf]) . '.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check6 = 'cbm.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check7 = 'bombeiromilitar.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check8 = 'corpodebombeiros.'  . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check9 = 'cbm' . strtolower($uf_array[$uf]) . '.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check10 = 'bm' . strtolower($uf_array[$uf]) . '.' . strtolower($uf_array[$uf]) . '.gov.br';
                $email_check11 = 'bme' . strtolower($uf_array[$uf]) . '.' . strtolower($uf_array[$uf]) . '.gov.br';
            }
            elseif ($instituicao == 8) {

                $email_check1 = 'mar.mil.br';
                $email_check2 = 'marinha.mil.br';
            }
            elseif ($instituicao == 9) {

                $email_check1 = 'eb.mil.br';
            }
            elseif ($instituicao == 10) {

                $email_check1 = 'fab.mil.br';
                $email_check2 = 'aer.mil.br';
            }

            $parts = explode('@', $email_institucional);

            if (strpos($parts[1], $email_check1) === false && strpos($parts[1], $email_check2) === false && strpos($parts[1], $email_check3) === false && strpos($parts[1], $email_check4) === false &&
                strpos($parts[1], $email_check5) === false && strpos($parts[1], $email_check6) === false && strpos($parts[1], $email_check7) === false && strpos($parts[1], $email_check8) === false &&
                strpos($parts[1], $email_check9) === false && strpos($parts[1], $email_check10) === false && strpos($parts[1], $email_check11) === false) {

                Erro('O e-mail institucional informado não corresponde às suas informações cadastrais.');
            }

            $result = DBRead('tb_usuarios', "WHERE id_usuario != {$id_usuario} AND email_institucional = '$email_institucional' AND analise_documental_concluida = 1 AND conta_excluida = 0 LIMIT 1");

            if (is_array($result)) {

                Erro('O e-mail institucional informado está cadastrado no nome de outro usuário.');
            }

            $codigo_confirmacao = sprintf("%06d", mt_rand(100000, 999999));

            DBExecute("UPDATE tb_usuarios SET email_cadastro = '$email_institucional', email_institucional = '$email_institucional', email_institucional_codigo = {$codigo_confirmacao} WHERE id_usuario = {$id_usuario} LIMIT 1");

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
            $mail->AddAddress($email_institucional);
            $mail->IsHTML(true);

            $mail->Subject = 'Código de confirmação';
            $mail->Body    = '<html><body><p><tt><kbd><span style="font-size:18px;"><b>Código de confirmação cadastral</b></br></span></kbd></tt></p><p>Este é um email automático enviado pelo sistema para confirmar seu cadastro na plataforma.</p><p>Use o código abaixo para confirmar seu cadastro no aplicativo.</p></br><p></p><p><strong><span style="font-size:14px;">Código de confirmação: <span style="font-size:26px;color:#78909c">'. $codigo_confirmacao .'</span></span></strong></p></br></body></html>';

            if ($mail->Send()) {

                Sucesso('E-mail enviado.');
            }
            else {

                Erro('Não foi possível enviar o e-mail com o código de confirmação. Tente novamente em alguns minutos.');
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
