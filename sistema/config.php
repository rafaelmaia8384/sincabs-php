<?php

	define('DB_HOSTNAME', '127.0.0.1');
	define('DB_USERNAME', 'sincabs_server');
	define('DB_PASSWORD', 'sincabs110786');
	define('DB_DATABASE', 'db_sincabs_oficial1');
	define('DB_CHARSET', 'utf8');

	define('ZOHO_SMTP', 'smtp.zoho.com');
	define('ZOHO_USER', 'app@sincabs.com.br');
	define('ZOHO_PASS', 'sincabs110786EMAIL');

	define('SYSTEM_NAME', 'SINCABS');

	define('MIN_WAIT_DIFF_DEVICE', 72);								//em horas

	define('LENGTH_ID', 8);
	define('LENGTH_HASH', 64);

	define('MAX_FILE_SIZE_UPLOAD', 1024);								//1024 kb ou 1 Mb

	define('PONTUACAO_COMPARTILHADO', 150);
	define('PONTUACAO_PESO_SUSPEITO', 10);
	define('PONTUACAO_PESO_BUSCA', 5);
	define('PONTUACAO_PESO_ACESSO', 1);

	define('MYSQL_ERROR_LOG', 		'../data/sql-error-log.txt');

	define('ERROR_VERSAO_APP',		'Atualize para a versão mais recente do aplicativo.');
	define('ERROR_MANUTENCAO', 		'O sistema está em manutenção. Tente novamente mais tarde.');
	define('ERROR_SISTEMA',			'Ocorreu um erro no sistema. Tente novamente em instantes.');
	define('ERROR_FRAUDE', 			'Acesso negado.');
	define('ERROR_OFFLINE', 		'Faça o login novamente para continuar.');
	define('ERROR_CAMPOS', 			'Preencha todos os campos.');
	define('ERROR_EMAIL', 			'O e-mail informado não é válido.');
	define('ERROR_CPF', 			'O CPF informado não é válido.');
	define('ERROR_SENHA', 			'Sua senha deve conter entre 6 e 20 caracteres.');
	define('ERROR_WRONG_PASSWORD', 	'Não foi possível entrar. Verifique seu CPF ou senha.');

?>
