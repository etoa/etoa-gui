<?php declare(strict_types=1);

if (!isset($app)) {
    $questSystemEnabled = false;
    $debug = true;
    $app = require __DIR__ .'/../../src/app.php';
    $app->boot();
}

/** @var \Twig\Environment $twig */
$twig = $app['twig'];

$successMessage = null;
$errorMessage = null;

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['INSTALL'])) {
    $_SESSION['INSTALL'] = [];
}

if (configFileExists(DBManager::getInstance()->getConfigFile())) {
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
        if (DBManager::getInstance()->connect(0, $dbCfg)) {
            $successMessage = 'Datenbankverbindung erfolgreich!';

            $_SESSION['INSTALL']['step']=2;
            $step = 2;
        } else {
            $errorMessage = 'Verbindung fehlgeschlagen! Fehler: '.mysql_error();
            $_SESSION['INSTALL']['step']=1;
            $step = 1;
        }
    } else {
        $errorMessage = 'Achtung! Du hast nicht alle Felder ausgef&uuml;lt!';
    }
} elseif($_POST['step2_submit'] ?? false) {
    $step = 2;

    $_SESSION['INSTALL']['round_name'] = $_POST['round_name'];
    $_SESSION['INSTALL']['round_url'] = $_POST['round_url'];
    $_SESSION['INSTALL']['loginserver_url'] = $_POST['loginserver_url'];
    $_SESSION['INSTALL']['referers'] = $_POST['referers'];

    if ($_POST['round_name'] != "") {
        $step = 3;
        $_SESSION['INSTALL']['step'] = 3;
    } else {
        $errorMessage = 'Achtung! Du hast nicht alle Felder ausgef&uuml;lt!';
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
    DBManager::getInstance()->connect(0, $dbCfg);

    $dbConfigString = json_encode($dbCfg, JSON_PRETTY_PRINT);

    $dbConfigStingEventHandler = '[mysql]
host = ' .$dbCfg['host']. '
database = ' .$dbCfg['dbname']. '
user = ' .$dbCfg['user']. '
password = ' .$dbCfg['password']. '
';
    $cfg = Config::getInstance();
    $cfg->set("referers",$_SESSION['INSTALL']['referers']);
    $cfg->set("roundname",$_SESSION['INSTALL']['round_name']);
    $cfg->set("roundurl",$_SESSION['INSTALL']['round_url']);
    $cfg->set("loginurl",$_SESSION['INSTALL']['loginserver_url']);

    writeConfigFile(DBManager::getInstance()->getConfigFile(), $dbConfigString);
    writeConfigFile(EVENTHANDLER_CONFIG_FILE_NAME, $dbConfigStingEventHandler);

    if (configFileExists(DBManager::getInstance()->getConfigFile())) {
        $_SESSION['INSTALL']['step'] = 1;
    }

    echo $twig->render('install/step3.html.twig', [
        'templateDir' => 'designs/official/Revolution',
        'currentStep' => $step,
        'successMessage' => 'Konfiguration gespeichert!',
        'errorMessage' => $errorMessage,
        'dbConfigFileMissing' => !configFileExists(DBManager::getInstance()->getConfigFile()),
        'dbConfigFile' => getConfigFilePath(DBManager::getInstance()->getConfigFile()),
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
    DBManager::getInstance()->connect(0, $dbCfg);

    // Migrate database
    $cnt = DBManager::getInstance()->migrate();
    if ($cnt > 0) {
        $successMessage = 'Datenbank migriert';

        // Load config defaults
        Config::restoreDefaults();
        Config::getInstance()->reload();
    }

    $cfg = Config::getInstance();

    if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
        $default_round_url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
        $default_referers = $default_round_url."\n".INSTALLER_DEFAULT_LOGINSERVER_URL;
    } else {
        $default_round_url = $cfg->get('roundurl');
        $default_referers = $cfg->get('referers');
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


