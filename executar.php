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

    $opcoes = array(

        //usuários
        '101' => 'usuarios/verificar-cpf.php',
        '102' => 'usuarios/cadastrar-usuario.php',
        '103' => 'usuarios/login.php',
        '104' => 'usuarios/enviar-documento-frente.php',
        '105' => 'usuarios/enviar-documento-verso.php',
        '106' => 'usuarios/cancelar-cadastro.php',
        '107' => 'usuarios/recuperar-senha-id-aparelho.php',
        '108' => 'usuarios/recuperar-senha-senha.php',
        '109' => 'usuarios/usuarios-mais-ativos.php',
        '110' => 'usuarios/perfil-usuario.php',
        '111' => 'usuarios/date-time-servidor.php',
        '112' => 'usuarios/meus-cadastros.php',
        '113' => 'usuarios/verificar-analise-documental.php',
        '114' => 'usuarios/verificar-uf.php',
        '115' => 'usuarios/trocar-imagem-perfil.php',
        '116' => 'usuarios/trocar-email.php',
        '117' => 'usuarios/enviar-sugestao.php',
        '118' => 'usuarios/excluir-perfil-usuario.php',
        '119' => 'usuarios/denunciar-usuario.php',
        '120' => 'usuarios/buscar-usuario.php',
        '121' => 'usuarios/verificar-mensagem-do-sistema.php',
        '122' => 'usuarios/enviar-telefone.php',
        '123' => 'usuarios/enviar-email-institucional.php',
        '124' => 'usuarios/enviar-codigo-confirmacao.php',
        '125' => 'usuarios/sincabs-compartilhado.php',

        //suspeitos
        '201' => 'suspeitos/cadastrar-suspeito.php',
        '202' => 'suspeitos/ultimos-cadastros.php',
        '203' => 'suspeitos/perfil-suspeito.php',
        '204' => 'suspeitos/perfil-suspeito-imagens.php',
        '205' => 'suspeitos/enviar-imagem.php',
        '206' => 'suspeitos/excluir-imagem.php',
        '207' => 'suspeitos/trocar-imagem-perfil.php',
        '208' => 'suspeitos/excluir-perfil-suspeito.php',
        '209' => 'suspeitos/buscar-suspeito.php',
        '210' => 'suspeitos/comentarios.php',
        '211' => 'suspeitos/enviar-comentario.php',
        '212' => 'suspeitos/excluir-comentario.php',
        '213' => 'suspeitos/editar-relato.php',
        '214' => 'suspeitos/editar-nome-alcunha.php',
        '215' => 'suspeitos/editar-area-de-atuacao.php',
        '216' => 'suspeitos/editar-historico-criminal.php',
        '217' => 'suspeitos/editar-nome-completo.php',
        '218' => 'suspeitos/editar-nome-da-mae.php',
        '219' => 'suspeitos/editar-cpf.php',
        '220' => 'suspeitos/editar-rg.php',
        '221' => 'suspeitos/editar-data-nascimento.php',
        '222' => 'suspeitos/denunciar-suspeito.php',

        //admin
        '301' => 'admin/obter-analises.php',
        '302' => 'admin/obter-informacoes-usuario.php',
        '303' => 'admin/concluir-analise.php',
        '304' => 'admin/bloquear-usuario.php',
        '305' => 'admin/desbloquear-usuario.php',
        'xxx' => 'admin/obter-denuncias-suspeito.php'
    );

    if (!empty($_POST['versao_app']) && !empty($_POST['opcao']) && !empty($_POST['codigo']) && !empty($_POST['check'])) {

        $versao_app = DBEscape($_POST['versao_app']);
        $opcao = DBEscape($_POST['opcao']);
        $codigo = DBEscape($_POST['codigo']);
        $check = DBEscape($_POST['check']);

        $result = DBRead('tb_sistema', 'WHERE id = 1', 'versao_app, em_manutencao');

        if (is_array($result)) {

            $versao = $result[0]['versao_app'];

            if ($versao > $versao_app) {

                Erro(ERROR_VERSAO_APP);
            }

            $manutencao = $result[0]['em_manutencao'];

            if ($manutencao != 0) {

                Erro(ERROR_MANUTENCAO);
            }
        }
        else {

            Erro(ERROR_SISTEMA);
        }

        if (array_key_exists($opcao, $opcoes)) {

            $postdata = base64_decode($mcrypt->decrypt($codigo));

            if ($check != md5($postdata)) {

                sincabsDie('Acesso negado.');
            }

            parse_str($postdata, $postdata_array);

            $_POST = $postdata_array;

            include ($opcoes[$opcao]);

            Erro(ERROR_SISTEMA); // só será chamado se ocorrer um erro dentro do include()
        }
        else {

            diesincabsDie('Acesso negado.');
        }
    }
    else {

        diesincabsDie('Acesso negado.');
    }

?>
