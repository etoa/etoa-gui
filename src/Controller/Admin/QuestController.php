<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Quest\DefaultRandomRegistry;
use EtoA\Quest\QuestPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestController extends AbstractController
{
    private DefaultRandomRegistry $registry;
    private QuestPresenter $presenter;

    public function __construct(DefaultRandomRegistry $registry, QuestPresenter $presenter)
    {
        $this->registry = $registry;
        $this->presenter = $presenter;
    }

    /**
     * @Route("/admin/quests/list", name="admin.quests.list")
     */
    public function list(): Response
    {
        $quests = $this->registry->getQuests();

        $rewards = [];
        foreach ($quests as $quest) {
            $rewards[$quest['id']] = $this->presenter->buildRewards($quest);
        }

        return $this->render('admin/quests/list.html.twig', [
            'quests' => $quests,
            'rewards' => $rewards,
        ]);
    }
}
