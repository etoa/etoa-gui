<?php

namespace EtoA\Legacy;

use EtoA\Core\Configuration\ConfigurationService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UtilityMethodProvider
{
    public function __construct(
        private readonly ConfigurationService  $config,
        private readonly UrlGeneratorInterface $router,
    )
    {
    }

    // TODO
    public function getLoginUrl($args = array()): string
    {
        $url = $this->config->get('loginurl');
        if (!$url) {
            $url = $this->router->generate('external.login');
            if (sizeof($args) > 0 && isset($args['page'])) {
                unset($args['page']);
            }
        }
        if (count($args) > 0) {
            foreach ($args as $k => $v) {
                if (!stristr($url, '?')) {
                    $url .= "?";
                } else {
                    $url .= "&";
                }
                $url .= $k . "=" . $v;
            }
        }
        return $url;
    }
}