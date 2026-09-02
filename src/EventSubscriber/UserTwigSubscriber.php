<?php

namespace EtoA\EventSubscriber;

use EtoA\BuddyList\BuddyListRepository;
use EtoA\Controller\Admin\MessageController;
use EtoA\Controller\Game\AllianceBaseController;
use EtoA\Controller\Game\AllianceBoardController;
use EtoA\Controller\Game\AllianceDiplomacyController;
use EtoA\Controller\Game\AllianceInternalController;
use EtoA\Controller\Game\BuddylistController;
use EtoA\Controller\Game\ContactController;
use EtoA\Controller\Game\HelpController;
use EtoA\Controller\Game\StatsController;
use EtoA\Controller\Game\TownhallController;
use EtoA\Controller\Game\UserConfigController;
use EtoA\Controller\Game\UserinfoController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetRepository;
use EtoA\Support\GameUtils;
use EtoA\Message\MessageRepository;
use EtoA\Message\ReportRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\GameVersionService;
use EtoA\Support\StringUtils;
use EtoA\Text\TextRepository;
use EtoA\Tutorial\TutorialUserProgressRepository;
use EtoA\Universe\Planet\PlanetManager;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserPropertiesRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use EtoA\Security\Player\CurrentPlayer;
use EtoA\Design\DesignService;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use EtoA\Controller\Game\SetupController;
use EtoA\Controller\Image\GalaxyMapImageController;
use EtoA\Fleet\ForeignFleetService;
use EtoA\UI\Tooltip;

class UserTwigSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TokenStorageInterface    $tokenStorage,
        private readonly UserPropertiesRepository $userPropertiesRepository,
        private readonly Security                 $security,
        private readonly TextRepository           $textRepo,
        private readonly GameVersionService       $versionService,
        private readonly BuddyListRepository      $buddyListRepository,
        private readonly Environment                    $twig,
        private readonly FleetRepository                $fleetRepository,
        private readonly MessageRepository              $messageRepository,
        private readonly ReportRepository               $reportRepository,
        private readonly DesignService                  $designService,
        private readonly ConfigurationService           $config,
        private readonly GameUtils                      $utilities,
        private readonly UrlGeneratorInterface          $router,
        private readonly ForeignFleetService            $foreignFleetLoader,
        private readonly PlanetRepository               $planetRepository,
        private readonly TutorialUserProgressRepository $tutorialUserProgressRepository,
        private readonly Tooltip                        $tooltip
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    //Controllers that can accessed before finishing the setup
    const WHITELIST = [
        SetupController::class,
        GalaxyMapImageController::class,
        HelpController::class,
        ContactController::class
    ];

    const ALLIANCE_BLOCKLIST = [
        AllianceInternalController::class,
        AllianceBoardController::class,
        AllianceBaseController::class,
        AllianceDiplomacyController::class
    ];

    public function onKernelRequest(RequestEvent $event):void
    {
        // Sub requests (e.g. the sub request per action of a live component batch call)
        // must not add the twig globals again: as soon as one of them has rendered a
        // template, twig is initialized and addGlobal() throws.
        if (!$event->isMainRequest()) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if ($token === null || !$token->getUser() instanceof CurrentPlayer) {
            return;
        }

        $request = $event->getRequest();
        $s = $request->getSession();
        $cu = $this->security->getUser();
        $ownFleetCount = $this->fleetRepository->count(['user'=>$cu->getData()]);
        $newMessages = $this->messageRepository->count(['deleted'=>0,'userTo'=>$cu->getId(),'read'=>0]);
        $newReports = $this->reportRepository->countUserUnread($cu->getData());
        $properties = $this->userPropertiesRepository->getOrCreateProperties($cu->getData());
        $page = $request->query->get('page', 'overview');
        $mode = $request->query->get('mode', '');
        $infoText = $this->textRepo->find('info');
        $allowed_ips = explode("\n", $this->config->get('offline_ips_allow'));
        $time = time();
        $controller = $event->getRequest()->attributes->get('_controller');

        if(!$controller)
            return;

        if(is_string($controller)) {
            $controller = explode('::', $controller);
        }
        else {
            $controller[0] = '';
        }

        $request->headers->set('cache-control','no-cache, must-revalidate');

        //TODO:refactor
        /*
         prev() - moves the internal pointer to, and outputs, the previous element in the array
        current() - returns the value of the current element in an array
        end() - moves the internal pointer to, and outputs, the last element in the array
        reset() - moves the internal pointer to the first element of the array
        each() - returns the current element key and value, and moves the internal pointer forward
         */
        if ($cu->getData()->isSetup()) {
            $userPlanets = $cu->getData()->getPlanets();
            $planets = [];
            $mainplanet = 0;
            foreach ($userPlanets as $planet) {
                $planets[] = $planet->getEntity()->getId();
                if ($planet->isMainPlanet()) {
                    $mainplanet = $planet->getEntity()->getId();
                }
            }

            $eid = $event->getRequest()->get('change_entity');
            if ($eid && in_array(3, $planets)) {
                $cpid = $eid;
                $s->set('cpid',$cpid) ;
            } elseif ($s->get('cpid') && in_array((int)$s->get('cpid'), $planets, true)) {
                $cpid = $s->get('cpid');
            } else {
                $cpid = $mainplanet;
                $s->set('cpid',$cpid);
            }

            $cp = $this->planetRepository->find($cpid);
            $pm = new PlanetManager($planets, $this->planetRepository);
        }



        if (isset($cp, $pm)) {
            $currentPlanetData = [
                'currentPlanetId' => $cp->getEntity()->getId(),
                'currentPlanetName' => $cp->getEntity()->toString(),
                'currentPlanetImage' => $cp->getImagePath('m'),
                'planetList' => $pm->getLinkList($s->get('cpid')),
                'nextPlanetId' => $pm->nextId($s->get('cpid')),
                'prevPlanetId' => $pm->prevId($s->get('cpid')),
                'selectField' => $pm->getSelectField($s->get('cpid')),
            ];
        } else {
            $currentPlanetData = [
                'currentPlanetId' => 0,
                'currentPlanetName' => 'Unbekannt',
                'planetList' => [],
                'nextPlanetId' => 0,
                'prevPlanetId' => 0,
                'selectField' => null,
            ];
        }

        $globals = array_merge($currentPlanetData, [
            'design' => $properties->getCssStyle(),
            'gameTitle' => $this->versionService->getGameIdentifier(),
            'templateDir' => '/' .$this->designService->getCurrentDesign(),
            'bodyTopStuff' => $this->tooltip->initHtml(),
            'ownFleetCount' => $ownFleetCount,
            'messages' => $newMessages,
            'newreports' => $newReports,
            'blinkMessages' => $properties->isMsgBlink(),
            'buddys' => $this->buddyListRepository->countFriendsOnline($cu->getId()),
            'buddyreq' => $this->buddyListRepository->hasPendingFriendRequest($cu->getId()),
            'fleetAttack' => $this->foreignFleetLoader->getVisibleFleets($cu->getData())->aggressiveCount,
            'enableKeybinds' => $properties->isEnableKeybinds(),
            'isAdmin' => $cu->getData()->getAdmin(),
            'userPoints' => StringUtils::formatNumber($cu->getData()->getPoints()),
            'userNick' => $cu->getData()->getNick(),
            'page' => $page,
            'mode' => $mode,
            'infoText' => $infoText->isEnabled() ? $infoText->getContent() : null,
            'viewportScale' => $_SESSION['viewportScale'] ?? 0,
            'fontSize' => ($_SESSION['viewportScale'] ?? 1) * 16 . "px",
            'helpBox' => $cu->getData()->getUserProperties()->isHelpBox(),
            'warnings' => $cu->getData()->getUserWarnings()
        ]);
        foreach ($globals as $key => $value) {
            $this->twig->addGlobal($key, $value);
        }

        // The closed flag decides, not the mere existence of a progress row: reopening a
        // tutorial only resets that flag, so checking for the row would ignore it.
        $userId = (int) $cu->getId();
        if (!$this->tutorialUserProgressRepository->hasReadTutorial($userId, 1)) {
            $this->twig->addGlobal('tutorial_id', 1);
        } else if ($cu->getdata()->isSetup() && !$this->tutorialUserProgressRepository->hasReadTutorial($userId, 2)) {
            $this->twig->addGlobal('tutorial_id', 2);
        } elseif ($cu->getdata()->isSetup() && $this->config->getInt('quest_system_enable')) {
            //$app['cubicle.quests.initializer']->initialize($this->getUser()->getId()); //TODO migrate quests
        }

        if ($this->config->getBoolean('offline') && !in_array($request->server->get('REMOTE_ADDR'), $allowed_ips, true)) {
            $text = $this->config->get('offline_message') ?
                BBCodeUtils::toHTML($this->config->get('offline_message')):
                'Das Spiel ist aufgrund von Wartungsarbeiten momentan offline! Schaue später nochmals vorbei!';
            $image = 'build/images/maintenance.jpg';
            $title = 'Spiel offline';

            $this->renderBlocked($event,$text,$image,$title);
        } // Login ist gesperrt
        elseif (!$this->config->getBoolean('enable_login') && !in_array($request->server->get('REMOTE_ADDR'), $allowed_ips, true)) {
            $text = 'Der Login momentan geschlossen!';
            $image = 'build/images/keychain.png';
            $title = 'Login geschlossen';

            $this->renderBlocked($event,$text,$image,$title);
        } // Login ist erlaubt aber noch zeitlich zu früh
        elseif ($this->config->getBoolean('enable_login') && $this->config->param1Int('enable_login') > time() && !in_array($request->server->get('REMOTE_ADDR'), $allowed_ips, true)) {
            $text = "Das Spiel startet am " . date("d.m.Y", $this->config->param1Int('enable_login')) . " ab " . date("H:i", $this->config->param1Int('enable_login')) . "!";
            $image = 'build/images/keychain.png';
            $title = 'Login noch geschlossen';

            $this->renderBlocked($event,$text,$image,$title);
        } // Zugriff von anderen als eigenem Server bzw Login-Server sperren
        elseif ($request->server->get('HTTP_REFERER') && !$this->utilities->refererAllowed()) {
            $text = "Der Zugriff auf das Spiel ist nur anderen internen Seiten aus möglich! Ein externes Verlinken direkt in das Game hinein ist nicht gestattet! Dein Referer: " . $request->server->get('HTTP_REFERER');
            $image = 'build/images/keychain.png';
            $title = 'Falscher Referer';

            $this->renderBlocked($event,$text,$image,$title);
        }
        elseif (
            $cu->getData()->getBlockedFrom() > 0 &&
            $cu->getData()->getBlockedFrom() < $time &&
            $cu->getData()->getBlockedTo() > $time
        ) {
            if(!in_array($controller[0],self::WHITELIST)) {
                $content = $this->twig->render('game/banned.html.twig');
                $response = new Response($content);
                $event->setResponse($response);
            }
        }
        elseif ($cu->getData()->getDeleted()>0) {
            if(!in_array($controller[0],[ContactController::class,UserConfigController::class])) {
                $content = $this->twig->render('game/deletion.html.twig');
                $response = new Response($content);
                $event->setResponse($response);
            }
        }
        elseif ($cu->getData()->getHmodFrom()>0) {
            if(!in_array($controller[0],[
                UserConfigController::class,
                MessageController::class,
                StatsController::class,
                TownhallController::class,
                BuddylistController::class,
                UserInfoController::class,
                ContactController::class,
                HelpController::class,
            ])) {
                $content = $this->twig->render('game/hmode.html.twig');
                $response = new Response($content);
                $event->setResponse($response);
            }
        }
    }

    public function onKernelController(ControllerEvent $event):void {
        $token = $this->tokenStorage->getToken();
        if ($token === null || !$token->getUser() instanceof CurrentPlayer) {
            return;
        }

        $cu = $this->security->getUser();

        $controller = $event->getRequest()->attributes->get('_controller');
        if(is_string($controller) && str_contains($controller, '::')) {
            $controller = explode('::', $controller);

            //redirect to setup if new account
            if($cu && !$cu->getData()->isSetup() && !in_array($controller[0],self::WHITELIST)) {
                $event->setController(fn() => new RedirectResponse(($this->router->generate('game.setup.race'))));
            }

            //block internal alliance routes if not in alliance
            if(in_array($controller[0], self::ALLIANCE_BLOCKLIST) && !$cu->getData()->getAlliance())
                $event->setController(fn() => new RedirectResponse(($this->router->generate('game.alliance'))));
        }
    }

    private function renderBlocked(RequestEvent $event,string $text, string $image, string $title):void
    {
        $content = $this->twig->render('game/blocked.html.twig', [
            'loginUrl' => $this->utilities->getLoginUrl(),
            'text' => $text,
            'image' => $image,
            'title'=> $title
        ]);
        $response = new Response($content);
        $event->setResponse($response);
    }
}