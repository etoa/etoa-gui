<?php

namespace EtoA\EventSubscriber;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Design\Design;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\FirewallMapInterface;
use Twig\Environment;

class ExternalSubscriber implements EventSubscriberInterface
{


    public function __construct(
        private readonly FirewallMapInterface $firewallMap,
        private readonly ConfigurationService $configurationService,
        private readonly Environment          $twig,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event):void
    {
        $request = $event->getRequest();
        $firewall = $this->firewallMap->getFirewallConfig($request);

        if(!$firewall->getAuthenticators()) {
            $globals = [
                'templateDir' => 'build/' . Design::DIRECTORY . '/official/' . $this->configurationService->get('default_css_style'),
                'enableKeybinds' => false,
                'viewportScale' => 0,
                'fontSize' => 16 . "px",
            ];
            foreach ($globals as $key => $value) {
                $this->twig->addGlobal($key, $value);
            }
        }
    }
}