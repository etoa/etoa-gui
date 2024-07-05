<?php

namespace EtoA\EventSubscriber;

use EtoA\BuddyList\BuddyListRepository;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Message\MessageRepository;
use EtoA\Message\ReportRepository;
use EtoA\Support\GameVersionService;
use EtoA\Support\StringUtils;
use EtoA\Text\TextRepository;
use EtoA\User\UserPropertiesRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use EtoA\Security\Player\CurrentPlayer;
use EtoA\Design\DesignService;

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
        private readonly DesignService            $designService
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null || !$token->getUser() instanceof CurrentPlayer) {
            return;
        }

        $request = $event->getRequest();

        if(!gettype(require_once $this->projectDir . '/src/xajax/xajax.inc.php') === 'object')
            $xajax = require_once $this->projectDir . '/src/xajax/xajax.inc.php';

        $cu = $this->security->getUser();
        $ownFleetCount = $this->fleetRepository->count(FleetSearch::create()->user($cu->getId()));
        $newMessages = $this->messageRepository->countNewForUser($cu->getId());
        $newReports = $this->reportRepository->countUserUnread($cu->getId());
        $properties = $this->userPropertiesRepository->getOrCreateProperties($cu->getId());
        $page = $request->query->get('page', 'overview');
        $mode = $request->query->get('mode', '');
        $infoText = $this->textRepo->find('info');

        if (isset($cp, $pm)) {
            $currentPlanetData = [
                'currentPlanetName' => $cp,
                'currentPlanetImage' => $cp->imagePath('m'),
                'planetList' => $pm->getLinkList($s->cpid, $page, $mode),
                'nextPlanetId' => $pm->nextId($s->cpid),
                'prevPlanetId' => $pm->prevId($s->cpid),
                'selectField' => $pm->getSelectField($s->cpid),
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
            #'fleetAttack' => check_fleet_incomming($cu->getId()),
            'fleetAttack' => 0,
            'enableKeybinds' => $properties->enableKeybinds,
            'isAdmin' => $cu->getData()->getAdmin(),
            'userPoints' => StringUtils::formatNumber($cu->getData()->getPoints()),
            'userNick' => $cu->getData()->getNick(),
            'page' => $page,
            'mode' => $mode,
            'infoText' => $infoText->isEnabled() ? $infoText->content : null,
            'viewportScale' => $_SESSION['viewportScale'] ?? 0,
            'fontSize' => ($_SESSION['viewportScale'] ?? 1) * 16 . "px",
            'helpBox' => false
        ]);
        foreach ($globals as $key => $value) {
            $this->twig->addGlobal($key, $value);
        }


    }
}