<?php

namespace EtoA\Support;

use EtoA\Core\Configuration\ConfigurationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GameUtils
{

    public function __construct(
        private readonly ConfigurationService  $config,
        private readonly UrlGeneratorInterface $router,
    )
    {}

    public function getLoginUrl(): string
    {
        $url = $this->config->get('loginurl');
        if (!$url) {
            $url = $this->router->generate('external.login');
        }
        return $url;
    }

    public function refererAllowed():bool
    {
        $request = Request::createFromGlobals();
        #die(var_dump($request));
        if ($request->server->get('HTTP_REFERER')) {
            // Referrers
            $referrers = explode("\n", $this->config->get('referers'));
            foreach ($referrers as $k => &$v) {
                $referrers[$k] = trim($v);
            }
            unset($v);
            $referrers[] = 'http://' . $request->server->get('HTTP_HOST');
            foreach ($referrers as &$rfr) {
                if (str_starts_with($request->server->get('HTTP_REFERER'), $rfr)) {
                    return true;
                }
            }
            unset($rfr);
        }
        return false;
    }
}