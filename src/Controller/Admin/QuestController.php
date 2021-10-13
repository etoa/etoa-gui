<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Quest\DefaultRandomRegistry;
use EtoA\Quest\QuestPresenter;
use EtoA\Quest\QuestRepository;
use LittleCubicleGames\Quests\Workflow\QuestDefinition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestController extends AbstractController
{
    private DefaultRandomRegistry $registry;
    private QuestPresenter $presenter;
    private QuestRepository $repository;

    public function __construct(DefaultRandomRegistry $registry, QuestPresenter $presenter, QuestRepository $repository)
    {
        $this->registry = $registry;
        $this->presenter = $presenter;
        $this->repository = $repository;
    }

    /**
     * @Route("/admin/quests/", name="admin.quests.search")
     */
    public function search(Request $request): Response
    {
        $userQuests = [];
        $search = false;
        if ($request->isMethod('POST')) {
            $userNick = null;
            if ($_POST['user_nick'] !== '') {
                $userNick = '%' . $_POST['user_nick'] . '%';
            }

            $getIntOrNull = fn (int $value) => $value > 0 ? $value : null;
            $userQuests = $this->repository->searchQuests($getIntOrNull($request->request->getInt('quest_id')), $getIntOrNull($request->request->getInt('user_id')), $request->request->get('quest_state'), $userNick);
            $search = true;
        }

        $questMap = [];
        foreach ($this->registry->getQuests() as $questDefinition) {
            $questMap[$questDefinition['id']] = $questDefinition['title'];
        }

        return $this->render('admin/quests/search.html.twig', [
            'quests' => $this->registry->getQuests(),
            'questStates' => QuestDefinition::STATES,
            'userQuests' => $userQuests,
            'search' => $search,
            'questMap' => $questMap,
        ]);
    }

    /**
     * @Route("/admin/quests/edit/{id}", name="admin.quests.edit")
     */
    public function edit(Request $request, int $id): Response
    {
        if ($request->isMethod('POST')) {
            if ($request->request->has('del')) {
                $this->repository->deleteQuest($id);
            } elseif ($request->request->has('save')) {
                $this->repository->updateQuest($id, $request->request->get('quest_state'));
            }
        }

        $quest = $this->repository->getQuest($id);

        $questMap = [];
        foreach ($this->registry->getQuests() as $questDefinition) {
            $questMap[$questDefinition['id']] = $questDefinition['title'];
        }

        return $this->render('admin/quests/edit.html.twig', [
            'quest' => $quest,
            'questStates' => QuestDefinition::STATES,
            'questMap' => $questMap,
        ]);
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
