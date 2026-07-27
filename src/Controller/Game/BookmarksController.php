<?php

namespace EtoA\Controller\Game;

use EtoA\Bookmark\BookmarkOrder;
use EtoA\Bookmark\BookmarkRepository;
use EtoA\Bookmark\FleetBookmarkRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Bookmark;
use EtoA\Entity\Entity;
use EtoA\Entity\FleetBookmark;
use EtoA\Entity\UserProperties;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookmarksController extends AbstractGameController
{
    public function __construct(
        private readonly BookmarkRepository $bookmarkRepository,
        private readonly FleetBookmarkRepository $fleetBookmarkRepository,
        private readonly ConfigurationService $config,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService,
        private readonly EntityRepository $entityRepository,
        private readonly UserRepository $userRepository,
        private readonly UserPropertiesRepository $userPropertiesRepository,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    //
    // Target favourites
    //

    #[Route('/game/bookmarks/target', name: 'game.bookmarks.target')]
    public function target(Request $request): Response
    {
        $user = $this->getUser()->getData();
        $properties = $this->userPropertiesRepository->getOrCreateProperties($user);

        $bookmark = new Bookmark();
        $formNew = $this->createAddForm($bookmark);
        $formNew->handleRequest($request);

        $msg = null;
        if ($formNew->isSubmitted() && $formNew->isValid()) {
            $coordinates = new EntityCoordinates(
                (int) $formNew->get('sectorX')->getData(),
                (int) $formNew->get('sectorY')->getData(),
                (int) $formNew->get('cellX')->getData(),
                (int) $formNew->get('cellY')->getData(),
                (int) $formNew->get('pos')->getData()
            );

            if (!$this->isDiscovered($coordinates)) {
                $msg['error'] = "Das Gebiet ist noch nicht erkundet!";
            } else {
                $entity = $this->entityRepository->findByCoordinates($coordinates);
                if ($entity === null) {
                    $msg['error'] = "Es existiert kein Objekt an den angegebenen Koordinaten!";
                } elseif ($this->bookmarkRepository->hasBookmark($user, $entity)) {
                    $msg['error'] = "Dieser Favorit existiert schon!";
                } else {
                    $bookmark->setUser($user);
                    $bookmark->setEntity($entity);

                    $this->bookmarkRepository->persist($bookmark);
                    $this->bookmarkRepository->save();

                    $msg['success'] = "Der Favorit wurde hinzugefügt!";

                    // Start with an empty form again after a successful save
                    $formNew = $this->createAddForm(new Bookmark());
                }
            }
        }

        $formSort = $this->createSortForm($properties);
        $formSort->handleRequest($request);
        if ($formSort->isSubmitted() && $formSort->isValid()) {
            $this->userRepository->save();
        }

        return $this->render('game/bookmarks/bookmarks_target.html.twig', [
            'form_new' => $formNew,
            'form_sort' => $formSort,
            'hasBookmarks' => $this->bookmarkRepository->count(['user' => $user]) > 0,
            'msg' => $msg,
        ]);
    }

    /**
     * Adds a favourite for an already known entity (used by the galaxy view).
     */
    #[Route('/game/bookmarks/add/{id}', name: 'game.bookmarks.add')]
    public function add(?Entity $entity = null): Response
    {
        $user = $this->getUser()->getData();

        if ($entity === null) {
            return $this->renderError("Es existiert kein Objekt an den angegebenen Koordinaten!");
        }

        if ($this->bookmarkRepository->hasBookmark($user, $entity)) {
            return $this->renderError("Dieser Favorit existiert schon!");
        }

        $bookmark = new Bookmark();
        $bookmark->setUser($user);
        $bookmark->setEntity($entity);
        $bookmark->setComment('-');

        $this->bookmarkRepository->persist($bookmark);
        $this->bookmarkRepository->save();

        return $this->render('game/success.html.twig', [
            'msg' => "Der Favorit wurde hinzugefügt!",
            'path' => $this->generateUrl('game.bookmarks.target'),
            'headline' => 'Favoriten',
        ]);
    }

    #[Route('/game/bookmarks/edit/{id}', name: 'game.bookmarks.edit')]
    public function edit(Request $request, ?Bookmark $bookmark = null): Response
    {
        if ($bookmark === null || $bookmark->getUser() !== $this->getUser()->getData()) {
            return $this->renderError('Datensatz nicht gefunden!');
        }

        $form = $this->createFormBuilder($bookmark)
            ->add('comment', TextareaType::class, [
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'rows' => 3,
                    'cols' => 60,
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Speichern'])
            ->getForm()
            ->handleRequest($request);

        $msg = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookmarkRepository->save();
            $msg['success'] = 'Gespeichert';
        }

        return $this->render('game/bookmarks/bookmarks_edit.html.twig', [
            'form' => $form,
            'bookmark' => $bookmark,
            'msg' => $msg,
        ]);
    }

    #[Route('/game/bookmarks/delete/{id}', name: 'game.bookmarks.delete')]
    public function delete(?Bookmark $bookmark = null): Response
    {
        if ($bookmark === null || $bookmark->getUser() !== $this->getUser()->getData()) {
            return $this->renderError('Datensatz nicht gefunden!');
        }

        $this->bookmarkRepository->remove($bookmark);
        $this->bookmarkRepository->save();

        return $this->render('game/success.html.twig', [
            'msg' => 'Gelöscht!',
            'path' => $this->generateUrl('game.bookmarks.target'),
            'headline' => 'Favoriten',
        ]);
    }

    //
    // Fleet favourites
    //

    #[Route('/game/bookmarks/fleet', name: 'game.bookmarks.fleet')]
    public function fleet(): Response
    {
        // The list itself (including starting a stored fleet) is the FleetBookmarkList component
        return $this->render('game/bookmarks/bookmarks_fleet.html.twig', [
            'msg' => null,
        ]);
    }

    #[Route('/game/bookmarks/new', name: 'game.bookmarks.new')]
    public function new(): Response
    {
        return $this->render('game/bookmarks/bookmarks_new.html.twig', [
            'bookmarkId' => null,
        ]);
    }

    #[Route('/game/bookmarks/fleet/edit/{id}', name: 'game.bookmarks.fleet.edit')]
    public function fleetEdit(?FleetBookmark $bookmark = null): Response
    {
        if ($bookmark === null || $bookmark->getUser() !== $this->getUser()->getData()) {
            return $this->renderError('Flottenfavorit konnte nicht gefunden werden!', 'game.bookmarks.fleet');
        }

        return $this->render('game/bookmarks/bookmarks_new.html.twig', [
            'bookmarkId' => $bookmark->getId(),
            'activeTab' => 'game.bookmarks.new',
        ]);
    }

    #[Route('/game/bookmarks/fleet/delete/{id}', name: 'game.bookmarks.fleet.delete')]
    public function fleetDelete(?FleetBookmark $bookmark = null): Response
    {
        if ($bookmark === null || $bookmark->getUser() !== $this->getUser()->getData()) {
            return $this->renderError('Flottenfavorit konnte nicht gefunden werden!', 'game.bookmarks.fleet');
        }

        $this->fleetBookmarkRepository->remove($bookmark);
        $this->fleetBookmarkRepository->save();

        return $this->render('game/success.html.twig', [
            'msg' => 'Gelöscht!',
            'path' => $this->generateUrl('game.bookmarks.fleet'),
            'headline' => 'Favoriten',
        ]);
    }

    //
    // Helpers
    //

    private function createAddForm(Bookmark $bookmark): FormInterface
    {
        return $this->formFactory->createNamedBuilder('bookmark_new', FormType::class, $bookmark)
            ->add('sectorX', ChoiceType::class, [
                'choices' => $this->range(1, $this->config->param1Int('num_of_sectors')),
                'mapped' => false,
            ])
            ->add('sectorY', ChoiceType::class, [
                'choices' => $this->range(1, $this->config->param2Int('num_of_sectors')),
                'mapped' => false,
            ])
            ->add('cellX', ChoiceType::class, [
                'choices' => $this->range(1, $this->config->param1Int('num_of_cells')),
                'mapped' => false,
            ])
            ->add('cellY', ChoiceType::class, [
                'choices' => $this->range(1, $this->config->param2Int('num_of_cells')),
                'mapped' => false,
            ])
            ->add('pos', ChoiceType::class, [
                'choices' => $this->range(0, $this->config->param2Int('num_planets')),
                'mapped' => false,
            ])
            ->add('comment', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'size' => 20,
                    'maxlength' => 200,
                    'placeholder' => 'Kommentar',
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Speichern'])
            ->getForm();
    }

    private function createSortForm(UserProperties $properties): FormInterface
    {
        $sort = [];
        foreach (BookmarkOrder::ALL_ORDERS as $value => $name) {
            $sort[$name] = $value;
        }

        return $this->formFactory->createNamedBuilder('bookmark_sort', FormType::class, $properties)
            ->add('itemOrderBookmark', ChoiceType::class, [
                'choices' => $sort,
                'label' => false,
            ])
            ->add('itemOrderWay', ChoiceType::class, [
                'choices' => [
                    'Aufsteigend' => 'ASC',
                    'Absteigend' => 'DESC',
                ],
                'label' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Sortieren'])
            ->getForm();
    }

    /**
     * @return array<int, int>
     */
    private function range(int $from, int $to): array
    {
        $choices = [];
        for ($i = $from; $i <= $to; $i++) {
            $choices[$i] = $i;
        }

        return $choices;
    }

    private function isDiscovered(EntityCoordinates $coordinates): bool
    {
        $absX = (($coordinates->sx - 1) * $this->config->param1Int('num_of_cells')) + $coordinates->cx;
        $absY = (($coordinates->sy - 1) * $this->config->param2Int('num_of_cells')) + $coordinates->cy;

        return $this->userUniverseDiscoveryService->discovered($this->getUser()->getData(), $absX, $absY);
    }

    private function renderError(string $message, string $route = 'game.bookmarks.target'): Response
    {
        return $this->render('game/error.html.twig', [
            'msg' => $message,
            'path' => $this->generateUrl($route),
            'headline' => 'Favoriten',
        ]);
    }
}
