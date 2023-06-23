<?php declare(strict_types=1);

namespace EtoA\Quest;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Quest\Initialization\QuestInitializer;
use EtoA\Tutorial\TutorialUserProgressRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class QuestInitializerSubscriber implements EventSubscriberInterface
{
    private TutorialUserProgressRepository $tutorialUserProgressRepository;
    private QuestInitializer $questInitializer;
    private ConfigurationService $config;

    public function __construct(TutorialUserProgressRepository $tutorialUserProgressRepository, QuestInitializer $questInitializer, ConfigurationService $config)
    {
        $this->tutorialUserProgressRepository = $tutorialUserProgressRepository;
        $this->questInitializer = $questInitializer;
        $this->config = $config;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$this->config->getBoolean('quest_system_enable')) {
            return;
        }

        $request = $event->getRequest();
        $currentUser = $request->attributes->get('currentUser');
        if ($currentUser instanceof \User && $currentUser->isSetup() && $this->tutorialUserProgressRepository->hasFinishedTutorial($currentUser->getId())) {
            $this->questInitializer->initialize($currentUser->getId());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => ['onKernelController', -512]];
    }
}
