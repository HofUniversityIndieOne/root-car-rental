<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function() {
    $rootDirectory = dirname(dirname(__DIR__));
    $loader = new \Symfony\Component\Dotenv\Dotenv();
    $loader->load(
        $rootDirectory . '/.env.default',
        $rootDirectory . '/.env'
    );

    $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default'] = array_merge(
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default'] ?? [],
        array_filter([
            'charset' => getenv('TYPO3_DB_CHARSET'),
            'dbname' => getenv('TYPO3_DB_DBNAME'),
            'driver' => getenv('TYPO3_DB_DRIVER'),
            'host' => getenv('TYPO3_DB_HOST'),
            'user' => getenv('TYPO3_DB_USER'),
            'password' => getenv('TYPO3_DB_PASSWORD'),
        ])
    );

    $GLOBALS['TYPO3_CONF_VARS']['MAIL'] = array_merge(
        $GLOBALS['TYPO3_CONF_VARS']['MAIL'] ?? [],
        array_filter([
            'transport' => getenv('TYPO3_MAIL_TRANSPORT'),
            'transport_smtp_encrypt' => getenv('TYPO3_MAIL_SMTP_ENCRYPT'),
            'transport_smtp_server' => getenv('TYPO3_MAIL_SMTP_SERVER'),
            'transport_smtp_password' => getenv('TYPO3_MAIL_SMTP_PASSWORD'),
            'transport_smtp_username' => getenv('TYPO3_MAIL_SMTP_USERNAME'),
        ])
    );

    $GLOBALS['TYPO3_CONF_VARS']['GFX'] = array_merge(
        $GLOBALS['TYPO3_CONF_VARS']['GFX'] ?? [],
        array_filter([
            'processor' => getenv('TYPO3_GFX_PROCESSOR'),
            'processor_colorspace' => getenv('TYPO3_GFX_PROCESSOR_COLORSPACE'),
            'processor_path' => getenv('TYPO3_GFX_PROCESSOR_PATH'),
            'processor_path_lzw' => getenv('TYPO3_GFX_PROCESSOR_PATH'),
        ])
    );

    $GLOBALS['TYPO3_CONF_VARS']['BE'] = array_merge(
        $GLOBALS['TYPO3_CONF_VARS']['BE'] ?? [],
        array_filter([
            'installToolPassword' => getenv('TYPO3_BE_INSTALL_TOOL_PASSWORD'),
        ])
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS'] = array_merge(
        $GLOBALS['TYPO3_CONF_VARS']['SYS'] ?? [],
        array_filter([
            'encryptionKey' => getenv('TYPO3_SYS_ENCRYPTION_KEY'),
        ])
    );
});
