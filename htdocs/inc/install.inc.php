<?php

declare(strict_types=1);

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\DB\DatabaseMigrationService;
use Twig\Environment;

if (!isset($app)) {
    $debug = true;
    $app = require __DIR__ . '/../../src/app.php';
    $app->boot();
}

/** @var Environment $twig */
$twig = $app['twig'];

$successMessage = null;
$errorMessage = null;

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['INSTALL'])) {
    $_SESSION['INSTALL'] = [];
}

if (configFileExists(\EtoA\Core\DoctrineServiceProvider::CONFIG_FILE)) {
    echo $twig->render('install/install.html.twig', [
        'currentStep' => 4,
        'successMessage' => 'Ihre Konfigurationsdatei existiert bereits!',
        'errorMessage' => $errorMessage,
        'templateDir' => 'designs/official/Revolution',
    ]);
    return;
}

if (isset($_POST['install_check'])) {
    $_SESSION['INSTALL']['db_server'] = $_POST['db_server'];
    $_SESSION['INSTALL']['db_name'] = $_POST['db_name'];
    $_SESSION['INSTALL']['db_user'] = $_POST['db_user'];
    $_SESSION['INSTALL']['db_password'] = $_POST['db_password'];

    if ($_POST['db_server'] != "" && $_POST['db_name'] != "" && $_POST['db_user'] != "" && $_POST['db_password'] != "") {
        $dbCfg = [
            'host' => $_SESSION['INSTALL']['db_server'],
            'dbname' => $_SESSION['INSTALL']['db_name'],
            'user' => $_SESSION['INSTALL']['db_user'],
            'password' => $_SESSION['INSTALL']['db_password'],
        ];
        $app['db.options'] = $dbCfg;
        try {
            /** @var \Doctrine\DBAL\Connection $connection */
            $connection = $app['db.factory']($dbCfg);
            $connection->connect();
            $successMessage = 'Datenbankverbindung erfolgreich!';

            $_SESSION['INSTALL']['step'] = 2;
            $step = 2;
        } catch (\Doctrine\DBAL\Exception\ConnectionException $ex) {
            $errorMessage = 'Verbindung fehlgeschlagen! Fehler: ' . $ex->getMessage();
            $_SESSION['INSTALL']['step'] = 1;
            $step = 1;
        }
    } else {
        $errorMessage = 'Achtung! Du hast nicht alle Felder ausgefüllt!';
    }
} elseif ($_POST['step2_submit'] ?? false) {
    $step = 2;

    $_SESSION['INSTALL']['round_name'] = $_POST['round_name'];
    $_SESSION['INSTALL']['round_url'] = $_POST['round_url'];
    $_SESSION['INSTALL']['loginserver_url'] = $_POST['loginserver_url'];
    $_SESSION['INSTALL']['referers'] = $_POST['referers'];

    if ($_POST['round_name'] != "") {
        $step = 3;
        $_SESSION['INSTALL']['step'] = 3;
    } else {
        $errorMessage = 'Achtung! Du hast nicht alle Felder ausgefült!';
    }
}

if (isset($_SESSION['INSTALL']['step'], $_GET['step']) && $_GET['step'] > 0) {
    $step = (int) $_GET['step'];
} else {
    $step = (int) ($_SESSION['INSTALL']['step'] ?? 1);
}

if ($step === 3) {
    $dbCfg = array(
        'host' => $_SESSION['INSTALL']['db_server'],
        'dbname' => $_SESSION['INSTALL']['db_name'],
        'user' => $_SESSION['INSTALL']['db_user'],
        'password' => $_SESSION['INSTALL']['db_password'],
    );
    $app['db.options'] = $dbCfg;

    $dbConfigString = json_encode($dbCfg, JSON_PRETTY_PRINT);

    $dbConfigStingEventHandler = '[mysql]
host = ' . $dbCfg['host'] . '
database = ' . $dbCfg['dbname'] . '
user = ' . $dbCfg['user'] . '
password = ' . $dbCfg['password'] . '
';

    /** @var ConfigurationService $config */
    $config = $app[ConfigurationService::class];

    $config->set("referers", $_SESSION['INSTALL']['referers']);
    $config->set("roundname", $_SESSION['INSTALL']['round_name']);
    $config->set("roundurl", $_SESSION['INSTALL']['round_url']);
    $config->set("loginurl", $_SESSION['INSTALL']['loginserver_url']);

    writeConfigFile(\EtoA\Core\DoctrineServiceProvider::CONFIG_FILE, $dbConfigString);
    writeConfigFile(EVENTHANDLER_CONFIG_FILE_NAME, $dbConfigStingEventHandler);

    if (configFileExists(\EtoA\Core\DoctrineServiceProvider::CONFIG_FILE)) {
        $_SESSION['INSTALL']['step'] = 1;
    }

    echo $twig->render('install/step3.html.twig', [
        'templateDir' => 'designs/official/Revolution',
        'currentStep' => $step,
        'successMessage' => 'Konfiguration gespeichert!',
        'errorMessage' => $errorMessage,
        'dbConfigFileMissing' => !configFileExists(\EtoA\Core\DoctrineServiceProvider::CONFIG_FILE),
        'dbConfigFile' => getConfigFilePath(\EtoA\Core\DoctrineServiceProvider::CONFIG_FILE),
        'dbConfigString' => $dbConfigString,

        'eventHandlerConfigFileMissing' => !configFileExists(EVENTHANDLER_CONFIG_FILE_NAME),
        'eventHandlerConfigFile' => getConfigFilePath(EVENTHANDLER_CONFIG_FILE_NAME),
        'eventHandlerConfigString' => $dbConfigStingEventHandler,

        'loginUrl' => getLoginUrl(),
    ]);
    return;
}

if ($step === 2) {
    $dbCfg = [
        'host' => $_SESSION['INSTALL']['db_server'],
        'dbname' => $_SESSION['INSTALL']['db_name'],
        'user' => $_SESSION['INSTALL']['db_user'],
        'password' => $_SESSION['INSTALL']['db_password'],
    ];
    $app['db.options'] = $dbCfg;

    // Migrate database
    ob_start();

    /** @var DatabaseMigrationService $databaseMigrationService */
    $databaseMigrationService = $app[DatabaseMigrationService::class];

    $cnt = $databaseMigrationService->migrate();
    ob_clean();
    if ($cnt > 0) {
        $successMessage = 'Datenbank migriert';

        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];

        // Load config defaults
        $config->restoreDefaults();
        $config->reload();
    }

    if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) {
        $default_round_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        $default_referers = $default_round_url . "\n" . INSTALLER_DEFAULT_LOGINSERVER_URL;
    } else {

        /** @var ConfigurationService $config */
        $config = $app[ConfigurationService::class];

        $default_round_url = $config->get('roundurl');
        $default_referers = $config->get('referers');
    }

    echo $twig->render('install/step2.html.twig', [
        'templateDir' => 'designs/official/Revolution',
        'currentStep' => $step,
        'successMessage' => $successMessage,
        'errorMessage' => $errorMessage,
        'round_name' => $_SESSION['INSTALL']['round_name'] ?? 'Runde X',
        'round_url' => $_SESSION['INSTALL']['round_url'] ?? $default_round_url,
        'loginserver_url' => $_SESSION['INSTALL']['loginserver_url'] ?? INSTALLER_DEFAULT_LOGINSERVER_URL,
        'referers' => $_SESSION['INSTALL']['referers'] ?? $default_referers,
        'default_round_url' => $default_round_url,
        'default_loginserver_url' => INSTALLER_DEFAULT_LOGINSERVER_URL,
    ]);
    return;
}

echo $twig->render('install/step1.html.twig', [
    'templateDir' => 'designs/official/Revolution',
    'currentStep' => $step,
    'successMessage' => $successMessage,
    'errorMessage' => $errorMessage,
    'db_server' => $_SESSION['INSTALL']['db_server'] ?? 'localhost',
    'db_name' => $_SESSION['INSTALL']['db_name'] ?? 'etoa_roundx',
    'db_user' => $_SESSION['INSTALL']['db_user'] ?? 'etoa_roundx',
    'db_password' => $_SESSION['INSTALL']['db_password'] ?? '',
]);
