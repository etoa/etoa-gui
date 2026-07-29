<?php declare(strict_types=1);

namespace EtoA\Controller\Game;

use EtoA\Alliance\AlliancePointsRepository;
use EtoA\Alliance\AllianceStatsRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceStats;
use EtoA\Entity\User;
use EtoA\Entity\UserStat;
use EtoA\Pagination\ArrayPaginator;
use EtoA\Pagination\SimplePagination;
use EtoA\Ranking\GameStatsGenerator;
use EtoA\Ranking\UserTitlesService;
use EtoA\User\UserPointsRepository;
use EtoA\User\UserRatingRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserStatRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractGameController
{
    public function __construct(
        private readonly UserStatRepository       $userStatsRepository,
        private readonly UserTitlesService        $userTitlesService,
        private readonly UserRatingRepository     $userRatingRepository,
        private readonly AllianceStatsRepository  $allianceStatsRepository,
        private readonly ConfigurationService     $configurationService,
        private readonly UserRepository           $userRepository,
        private readonly GameStatsGenerator       $gameStatsGenerator,
        private readonly UserPointsRepository     $userPointsRepository,
        private readonly AlliancePointsRepository $alliancePointsRepository,
        private readonly string                   $cacheDir
    )
    {
    }

    #[Route("/game/stats/total", name: 'game.stats.total')]
    public function total(): Response
    {
        return $this->renderDefault('points', 'Gesamtpunkte');
    }

    #[Route("/game/stats/buildings", name: 'game.stats.buildings')]
    public function buildings(): Response
    {
        return $this->renderDefault('buildingPoints', 'Gebäudepunkte');
    }

    #[Route("/game/stats/tech", name: 'game.stats.tech')]
    public function tech(): Response
    {
        return $this->renderDefault('techPoints', 'Technologiepunkte');
    }

    #[Route("/game/stats/ships", name: 'game.stats.ships')]
    public function ships(): Response
    {
        return $this->renderDefault('shipPoints', 'Schiffspunkte');
    }

    #[Route("/game/stats/exp", name: 'game.stats.exp')]
    public function exp(): Response
    {
        return $this->renderDefault('expPoints', 'Erfahrungspunkte');
    }


    #[Route("/game/stats/battles", name: 'game.stats.battles')]
    public function battles(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('search', TextType::class, [
                'attr' => [
                    'onKeyUp' => "if(window.mytimeout) window.clearTimeout(window.mytimeout);
                           window.mytimeout = window.setTimeout(()=>{loadingMsg('statsTable','Suche Spieler...');event.target.parentElement.submit()}, 1500);",
                    'class' => "search"
                ],
                'mapped' => false,
                'required' => false
            ])
            ->add('nick', SubmitType::class, [
                'label' => $this->getUser()->getData()->getNick()
            ])
            ->getForm()
            ->handleRequest($request);

        $filter = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $nick = $form->get('nick')->isClicked() ? $this->getUser()->getData()->getNick() : $form->get('search')->getData();
            $user = $this->userRepository->findOneBy(['nick' => $nick]);
            $filter['user'] = $user;
        }

        /** @var UserStat[] $stats */
        $stats = $this->userRatingRepository->findBy($filter, ['battleRating' => 'DESC']);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($stats, $currentPage, $this->configurationService->getInt('stats_num_rows'));
        $pagination = new SimplePagination($paginator);

        return $this->render('game/stats/stats_battles.html.twig', [
            'paginator' => $paginator,
            'pagination' => $pagination,
            'form' => $form
        ]);
    }

    #[Route("/game/stats/trade", name: 'game.stats.trade')]
    public function trade(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('search', TextType::class, [
                'attr' => [
                    'onKeyUp' => "if(window.mytimeout) window.clearTimeout(window.mytimeout);
                           window.mytimeout = window.setTimeout(()=>{loadingMsg('statsTable','Suche Spieler...');event.target.parentElement.submit()}, 1500);",
                    'class' => "search"
                ],
                'mapped' => false,
                'required' => false
            ])
            ->add('nick', SubmitType::class, [
                'label' => $this->getUser()->getData()->getNick()
            ])
            ->getForm()
            ->handleRequest($request);

        $filter = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $nick = $form->get('nick')->isClicked() ? $this->getUser()->getData()->getNick() : $form->get('search')->getData();
            $user = $this->userRepository->findOneBy(['nick' => $nick]);
            $filter['user'] = $user;
        }

        /** @var UserStat[] $stats */
        $stats = $this->userRatingRepository->findBy($filter, ['tradeRating' => 'DESC']);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($stats, $currentPage, $this->configurationService->getInt('stats_num_rows'));
        $pagination = new SimplePagination($paginator);

        return $this->render('game/stats/stats_trade.html.twig', [
            'paginator' => $paginator,
            'pagination' => $pagination,
            'form' => $form
        ]);
    }

    #[Route("/game/stats/diplomacy", name: 'game.stats.diplomacy')]
    public function diplomacy(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('search', TextType::class, [
                'attr' => [
                    'onKeyUp' => "if(window.mytimeout) window.clearTimeout(window.mytimeout);
                           window.mytimeout = window.setTimeout(()=>{loadingMsg('statsTable','Suche Spieler...');event.target.parentElement.submit()}, 1500);",
                    'class' => "search"
                ],
                'mapped' => false,
                'required' => false
            ])
            ->add('nick', SubmitType::class, [
                'label' => $this->getUser()->getData()->getNick()
            ])
            ->getForm()
            ->handleRequest($request);

        $filter = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $nick = $form->get('nick')->isClicked() ? $this->getUser()->getData()->getNick() : $form->get('search')->getData();
            $user = $this->userRepository->findOneBy(['nick' => $nick]);
            $filter['user'] = $user;
        }

        /** @var UserStat[] $stats */
        $stats = $this->userRatingRepository->findBy($filter, ['diplomacyRating' => 'DESC']);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($stats, $currentPage, $this->configurationService->getInt('stats_num_rows'));
        $pagination = new SimplePagination($paginator);

        return $this->render('game/stats/stats_diplomacy.html.twig', [
            'paginator' => $paginator,
            'pagination' => $pagination,
            'form' => $form
        ]);
    }

    #[Route("/game/stats/alliances", name: 'game.stats.alliances')]
    public function alliances(Request $request): Response
    {
        $sort = $request->query->getAlnum('sort') ? $request->query->getAlnum('sort') : 'points';
        $order = $request->query->get('order') === 'ASC' ? 'ASC' : 'DESC';

        /** @var AllianceStats[] $stats */
        $stats = $this->allianceStatsRepository->findBy([], [$sort => $order]);

        return $this->render('game/stats/stats_alliance.html.twig', [
            'stats' => $stats
        ]);
    }

    #[Route("/game/stats/base", name: 'game.stats.base')]
    public function base(Request $request): Response
    {
        $sort = $request->query->getAlnum('sort') ? $request->query->getAlnum('sort') : 'points';
        $order = $request->query->get('order') === 'ASC' ? 'ASC' : 'DESC';

        /** @var AllianceStats[] $stats */
        $stats = $this->allianceStatsRepository->findBy([], [$sort => $order]);

        return $this->render('game/stats/stats_base.html.twig', [
            'stats' => $stats
        ]);
    }

    #[Route("/game/stats/titles", name: 'game.stats.titles')]
    public function titles(): Response
    {
        return $this->render('game/stats/stats_titles.html.twig', [
            'titles' => $this->userTitlesService->getTitles(),
        ]);
    }

    #[Route("/game/stats/pillory", name: 'game.stats.pillory')]
    public function pillory(): Response
    {
        return $this->render('game/stats/stats_pillory.html.twig', [
            'stats' => $this->userRepository->getPillory(),
        ]);
    }

    #[Route("/game/stats/gamestats", name: 'game.stats.gamestats')]
    public function gamestats(): Response
    {
        $cacheDir = $this->cacheDir;
        $img = is_file($cacheDir . GameStatsGenerator::USER_STATS_FILE);

        return $this->render('game/stats/stats_game.html.twig', [
            'stats' => $this->gameStatsGenerator->generate(),
            'img' => $img
        ]);
    }

    #[Route("/game/stats/userdetail/{id}", name: 'game.stats.userdetail')]
    public function userdetail(?User $user = null): Response
    {
        if ($user) {
            return $this->render('game/stats/stats_userdetail.html.twig', [
                'user' => $user,
                'pointEntries' => $this->userPointsRepository->getPoints($user, 48)
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Datensatz wurde nicht gefunden!',
            'path' => $this->generateUrl('game.stats.total'),
            'headline' => 'Statistiken'
        ]);
    }

    #[Route("/game/stats/alliancedetail/{id}", name: 'game.stats.alliancedetail')]
    public function alliancedetail(?Alliance $alliance = null): Response
    {
        if ($alliance) {
            return $this->render('game/stats/stats_alliancedetail.html.twig', [
                'alliance' => $alliance,
                'pointEntries' => $this->alliancePointsRepository->getPoints($alliance, 48)
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Datensatz wurde nicht gefunden!',
            'path' => $this->generateUrl('game.stats.total'),
            'headline' => 'Statistiken'
        ]);
    }

    private function renderDefault(string $value, string $title): Response
    {
        $request = Request::createFromGlobals();

        $form = $this->createFormBuilder()
            ->add('search', TextType::class, [
                'attr' => [
                    'onKeyUp' => "if(window.mytimeout) window.clearTimeout(window.mytimeout);
                           window.mytimeout = window.setTimeout(()=>{loadingMsg('statsTable','Suche Spieler...');event.target.parentElement.submit()}, 1500);",
                    'class' => "search"
                ],
                'mapped' => false,
                'required' => false
            ])
            ->add('nick', SubmitType::class, [
                'label' => $this->getUser()->getData()->getNick()
            ])
            ->getForm()
            ->handleRequest($request);

        $filter = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $filter['nick'] = $form->get('nick')->isClicked() ? $this->getUser()->getData()->getNick() : $form->get('search')->getData();
        }

        $sort = $request->query->getAlnum('sort') ? $request->query->getAlnum('sort') : $value;
        $order = $request->query->get('order') === 'ASC' ? 'ASC' : 'DESC';

        /** @var UserStat[] $stats */
        $stats = $this->userStatsRepository->findBy($filter, [$sort => $order]);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($stats, $currentPage, $this->configurationService->getInt('stats_num_rows'));
        $pagination = new SimplePagination($paginator);

        return $this->render('game/stats/stats_default.html.twig', [
            'paginator' => $paginator,
            'value' => $value,
            'tableTitle' => $title,
            'pagination' => $pagination,
            'form' => $form
        ]);
    }
}
