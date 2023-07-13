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

        $request = $this->requestStack->getCurrentRequest();
        $routeName = $request->attributes->get('_route');
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
            if ($request->request->has('reg_key_auth_submit')) {
                if ($request->request->get('reg_key_auth_value') === $this->config->get('register_key')) {
                    $this->userSession->reg_key_auth = $this->config->get('register_key');
                } else {
                    $this->addFlash('error', 'Falscher Schlüssel!');
                }
            }
            if ($this->userSession->reg_key_auth !== $this->config->get('register_key')) {
                return $this->render('external/key-required.html.twig', [
                    'currentUrl' => $this->generateUrl($routeName),
                ]);
            }
        }

        return $callback();
    }
}
