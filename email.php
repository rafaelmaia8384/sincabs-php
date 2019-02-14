<?php

    require_once 'sistema/PHPMailer_5.2.4/class.phpmailer.php';

    $mail = new PHPMailer;

    $mail->IsSMTP();
    $mail->Host = 'smtp.zoho.com';
    $mail->SMTPAuth = true;
    $mail->Port = 465;
    $mail->Username = 'app@sincabs.com.br';
    $mail->Password = 'sincabs110786EMAIL';
    $mail->SMTPSecure = 'ssl';
    $mail->CharSet = 'UTF-8';

    $mail->From = 'app@sincabs.com.br';
    $mail->FromName = 'Sincabs';
    $mail->AddAddress('rafaelpvm@hotmail.com');
    $mail->IsHTML(false);

    $mail->Subject = 'Teste de envio.';
    $mail->Body    = 'Testando o envio do email.';

    if ($mail->Send()) {

        print('Ok');
    }
    else {

        print('Erro' . $mail->ErrorInfo);
    }

    die();
?>
