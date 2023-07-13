<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Design\Design;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractLegacyShowController extends AbstractLegacyController
{
    protected ?string $pageTitle = null;

    protected function handle(callable $callback): Response
    {
        $this->bootstrap();

        $routeName = $this->requestStack->getCurrentRequest()->attributes->get('_route');
        $xajax = require_once $this->projectDir . '/src/xajax/xajax.inc.php';
        $globals = [
            'gameTitle' => $this->versionService->getGameIdentifier() . ($this->pageTitle !== null ? ' - ' . $this->pageTitle : ''),
            'templateDir' => '/' . Design::DIRECTORY . '/official/' . $this->config->get('default_css_style'),
            'bodyTopStuff' => getInitTT(),
            'xajaxJS' => $xajax->getJavascript(),
            'enableKeybinds' => false,
            'viewportScale' => 0,
            'fontSize' => 16 . "px",
        ];
        foreach ($globals as $key => $value) {
            $this->twig->addGlobal($key, $value);
        }

        // Handle case if outgame key is set
        if ($this->config->filled('register_key')) {
            $invalidKey = false;
            if (isset($_POST['reg_key_auth_submit'])) {
                if ($_POST['reg_key_auth_value'] === $this->config->get('register_key')) {
                    $this->userSession->reg_key_auth = $this->config->get('register_key');
                } else {
                    $invalidKey = true;
                }
            }

            if ($this->userSession->reg_key_auth !== $this->config->get('register_key')) {
                return $this->render('external/key-required.html.twig', [
                    'page' => $_GET['index'],
                    'invalidKey' => $invalidKey,
                ]);
            }
        }

        return $callback();
    }
}
