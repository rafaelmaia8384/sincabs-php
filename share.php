<?php

    if (!empty($_GET['code'])) {

        $parts = explode('-', $_GET['code']);

        if (ip2long($_SERVER['REMOTE_ADDR']) != $parts[0] && count($parts) == 3) {

            require 'sistema/config.php';
            require 'sistema/connection.php';
            require 'sistema/database.php';

            date_default_timezone_set('America/Araguaina');

            $code1 = $parts[0] . '-' . $parts[1];
            $check = crc32($code1);

            if ($parts[2] == $check) {

                $code = DBEscape($_GET['code']);
                $parts = explode('-', $code);

                $id_usuario = $parts[1];

                $result = DBRead('tb_usuarios', "WHERE id_usuario = {$id_usuario} AND conta_excluida = 0 LIMIT 1", 'id');

                if (is_array($result)) {

                    DBExecute("UPDATE tb_usuarios SET sincabs_compartilhado = 1 WHERE id_usuario = {$id_usuario} LIMIT 1");
                }
            }
        }
    }

    header("Location: https://play.google.com/store/apps/details?id=br.com.sincabs.appsincabs");
    exit();

?>
