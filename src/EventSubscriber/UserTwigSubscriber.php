<?php

namespace EtoA\EventSubscriber;

use EtoA\BuddyList\BuddyListRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Support\GameUtils;
use EtoA\Message\MessageRepository;
use EtoA\Message\ReportRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\GameVersionService;
use EtoA\Support\StringUtils;
use EtoA\Text\TextRepository;
use EtoA\Tutorial\TutorialManager;
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
use EtoA\Fleet\ForeignFleetLoader;
use EtoA\User\UserWarningRepository;

class UserTwigSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TokenStorageInterface    $tokenStorage,
        private readonly UserPropertiesRepository $userPropertiesRepository,
        private readonly Security                 $security,
        private readonly TextRepository           $textRepo,
        private readonly GameVersionService       $versionService,
        private readonly BuddyListRepository      $buddyListRepository,
        private readonly Environment              $twig,
        private readonly FleetRepository          $fleetRepository,
        private readonly MessageRepository        $messageRepository,
        private readonly ReportRepository         $reportRepository,
        private readonly string                   $projectDir,
        private readonly DesignService            $designService,
        private readonly ConfigurationService     $config,
        private readonly GameUtils                $utilities,
        private readonly UrlGeneratorInterface    $router,
        private readonly TutorialManager          $tutorialManager,
        private readonly ForeignFleetLoader       $foreignFleetLoader,
        private readonly PlanetRepository         $planetRepository,
        private readonly UserWarningRepository    $userWarningRepository,
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
        GalaxyMapImageController::class
    ];

    public function onKernelRequest(RequestEvent $event):void
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null || !$token->getUser() instanceof CurrentPlayer) {
            return;
        }

        $request = $event->getRequest();
        $s = $request->getSession();
        $cu = $this->security->getUser();
        $ownFleetCount = $this->fleetRepository->count(FleetSearch::create()->user($cu->getId()));
        $newMessages = $this->messageRepository->countNewForUser($cu->getId());
        $newReports = $this->reportRepository->countUserUnread($cu->getId());
        $properties = $this->userPropertiesRepository->getOrCreateProperties($cu->getId());
        $page = $request->query->get('page', 'overview');
        $mode = $request->query->get('mode', '');
        $infoText = $this->textRepo->find('info');
        $allowed_ips = explode("\n", $this->config->get('offline_ips_allow'));

        $request->headers->set('cache-control','no-cache, must-revalidate');

        if (!isCLI()) {
            if(!gettype(require_once $this->projectDir . '/src/xajax/xajax.inc.php') == 'object')
                $xajax = require_once $this->projectDir . '/src/xajax/xajax.inc.php';
        }

        if ($cu->getData()->isSetup()) {
            $userPlanets = $this->planetRepository->getUserPlanets($cu->getId());
            $planets = [];
            $mainplanet = 0;
            foreach ($userPlanets as $planet) {
                $planets[] = $planet->id;
                if ($planet->mainPlanet) {
                    $mainplanet = $planet->id;
                }
            }

            $eid = isset($_GET['change_entity']) ? (int)$_GET['change_entity'] : 0;
            if ($eid > 0 && in_array($eid, $planets, true)) {
                $cpid = $eid;
                $s->set('cpid',$cpid) ;
            } elseif ($s->get('cpid') && in_array((int)$s->get('cpid'), $planets, true)) {
                $cpid = $s->get('cpid');
            } else {
                $cpid = $mainplanet;
                $s->set('cpid',$cpid);
            }

            $cp = $this->planetRepository->find($cpid);
            $pm = new \EtoA\Legacy\PlanetManager($planets, $this->planetRepository);
        }

        if (isset($cp, $pm)) {
            $currentPlanetData = [
                'currentPlanetName' => $cp->name,
                'currentPlanetImage' => $cp->getImagePath('m'),
                'planetList' => $pm->getLinkList($s->get('cpid')),
                'nextPlanetId' => $pm->nextId($s->get('cpid')),
                'prevPlanetId' => $pm->prevId($s->get('cpid')),
                'selectField' => $pm->getSelectField($s->get('cpid')),
            ];
        } else {
            $currentPlanetData = [
                'currentPlanetName' => 'Unbekannt',
                'planetList' => [],
                'nextPlanetId' => 0,
                'prevPlanetId' => 0,
                'selectField' => null,
            ];
        }

        $globals = array_merge($currentPlanetData, [
            'design' => $properties->cssStyle,
            'gameTitle' => $this->versionService->getGameIdentifier(),
            'xajaxJS' => isset($xajax)?$xajax->getJavascript():null,
            'templateDir' => '/' .$this->designService->getCurrentDesign(),
            'bodyTopStuff' => getInitTT(),
            'ownFleetCount' => $ownFleetCount,
            'messages' => $newMessages,
            'newreports' => $newReports,
            'blinkMessages' => $properties->msgBlink,
            'buddys' => $this->buddyListRepository->countFriendsOnline($cu->getId()),
            'buddyreq' => $this->buddyListRepository->hasPendingFriendRequest($cu->getId()),
            'fleetAttack' => $this->foreignFleetLoader->getVisibleFleets($cu->getId())->aggressiveCount,
            'enableKeybinds' => $properties->enableKeybinds,
            'isAdmin' => $cu->getData()->getAdmin(),
            'userPoints' => StringUtils::formatNumber($cu->getData()->getPoints()),
            'userNick' => $cu->getData()->getNick(),
            'page' => $page,
            'mode' => $mode,
            'infoText' => $infoText->isEnabled() ? $infoText->content : null,
            'viewportScale' => $_SESSION['viewportScale'] ?? 0,
            'fontSize' => ($_SESSION['viewportScale'] ?? 1) * 16 . "px",
            'helpBox' => false,
            'warnings' => $this->userWarningRepository->search(\EtoA\User\UserWarningSearch::create()->userId($cu->getId()))
        ]);
        foreach ($globals as $key => $value) {
            $this->twig->addGlobal($key, $value);
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

        if (!$this->tutorialManager->hasReadTutorial($cu->getId(), 1)) {
            $this->twig->addGlobal('tutorial_id', 1);
        } else if ($cu->getdata()->isSetup() && !$this->tutorialManager->hasReadTutorial($cu->getId(), 2)) {
            $this->twig->addGlobal('tutorial_id', 2);
        } elseif ($cu->getdata()->isSetup() && $this->tutorialManager->hasReadTutorial($cu->getId(), 2) && $this->config->getInt('quest_system_enable')) {
            //$app['cubicle.quests.initializer']->initialize($this->getUser()->getId()); //TODO migrate quests
        }
    }

    public function onKernelController(ControllerEvent $event):void {
        $token = $this->tokenStorage->getToken();
        if ($token === null || !$token->getUser() instanceof CurrentPlayer) {
            return;
        }

        $cu = $this->security->getUser();
        if($cu && !$cu->getData()->isSetup() && !in_array($event->getControllerReflector()->class,self::WHITELIST)) { //TODO $page != "help" && $page != "contact"
            $event->setController(fn() => new RedirectResponse(($this->router->generate('game.setup.race'))));
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