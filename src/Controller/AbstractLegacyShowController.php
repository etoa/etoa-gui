<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Design\Design;
use EtoA\Legacy\User;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractLegacyShowController extends AbstractLegacyController
{
    private array $indexpage = [
        'login' => [
            'url' => '?index=login',
            'label' => 'Einloggen'
        ],
        'register' => [
            'url' => '?index=register',
            'label' => 'Registrieren'
        ],
        'pwforgot' => [
            'url' => '?index=pwforgot',
            'label' => 'Passwort'
        ],
        'contact' => [
            'url' => '?index=contact',
            'label' => 'Kontakt'
        ]
    ];

    protected function handle(callable $callback): Response
    {
        ob_start();

        $this->bootstrap();

        // Set default page / action variables
        $index = $_GET['index'] ?? null;
        $mode = $_GET['mode'] ?? null;

        $loggedIn = false;
        $s = $this->userSession;
        if ($s->validate(false)) {
            $cu = new User($s->getUserId());
            $loggedIn = (bool)$cu->isValid;
        }

        $properties = isset($cu) ? $this->userPropertiesRepository->getOrCreateProperties($cu->id) : null;

        $design = Design::DIRECTORY . '/official/' . $this->config->get('default_css_style');
        if (isset($cu) && filled($properties->cssStyle)) {
            if (is_dir(Design::DIRECTORY . '/official/' . $properties->cssStyle)) {
                $design = Design::DIRECTORY . '/official/' . $properties->cssStyle;
            }
        }
        define('CSS_STYLE', $design);

//        $xajax = require_once $projectDir . '/src/xajax/xajax.inc.php';
//        $twig->addGlobal('xajaxJS', $xajax->getJavascript());
        $globals = [
            'gameTitle' => $this->versionService->getGameIdentifier() . (isset($this->indexpage[$index]) ? ' - ' . $this->indexpage[$index]['label'] : ''),
            'templateDir' => '/' . CSS_STYLE,
            'bodyTopStuff' => getInitTT(),
            'prevPlanetId' => null,
            'nextPlanetId' => null,
            'enableKeybinds' => false,
            'mode' => $mode,
            'viewportScale' => $_SESSION['viewportScale'] ?? 0,
            'fontSize' => ($_SESSION['viewportScale'] ?? 1) * 16 . "px",
        ];

        foreach ($globals as $key => $value) {
            $this->twig->addGlobal($key, $value);
        }

        //
        // Page content
        //

        $show = true;
        $invalidKey = false;
        // Handle case if outgame key is set
        if ($this->config->filled('register_key')) {
            if (isset($_POST['reg_key_auth_submit'])) {
                if ($_POST['reg_key_auth_value'] === $this->config->get('register_key')) {
                    $s->reg_key_auth = $this->config->get('register_key');
                } else {
                    $invalidKey = true;
                }
            }

            if ($s->reg_key_auth !== $this->config->get('register_key')) {
                $show = false;
            }
        }

        if ($loggedIn) {
            $show = true;
        }

        if ($show) {
            $callback();
        } else {
            echo $this->twig->render('external/key-required.html.twig', [
                'page' => $_GET['index'],
                'invalidKey' => $invalidKey,
            ]);
        }

        return new Response(ob_get_clean());
    }
}
