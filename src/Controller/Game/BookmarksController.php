<?php

namespace EtoA\Controller\Game;

use EtoA\Bookmark\BookmarkOrder;
use EtoA\Bookmark\BookmarkRepository;
use EtoA\Bookmark\FleetBookmarkRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Bookmark;
use EtoA\Form\Validation\AlphaDotsOrUnderlinesConstraint;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookmarksController extends AbstractGameController
{
    public function __construct(
        private readonly BookmarkRepository $bookmarkRepository,
        private readonly ConfigurationService $config,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService,
        private readonly EntityRepository $entityRepository,
        private readonly UserRepository $userRepository,
        private readonly FleetBookmarkRepository $fleetBookmarkRepository
    )
    {}

    #[Route('/game/bookmarks/target', name: 'game.bookmarks.target')]
    public function target(Request $request): Response {
        $user = $this->getUser()->getData();
        $properties = $user->getUserProperties();
        $bookmarks = $this->bookmarkRepository->findForUser($user, new BookmarkOrder($properties->getItemOrderBookmark(), $properties->getItemOrderWay()));

        $sectorX = [];
        $sectorY = [];
        $cellX = [];
        $cellY = [];
        $planet = [];

        for ($x = 1; $x <= $this->config->param1Int('num_of_sectors'); $x++) {
            $sectorX[$x] = $x;
        }

        for ($y = 1; $y <= $this->config->param2Int('num_of_sectors'); $y++) {
            $sectorY[$y] = $y;
        }

        for ($x = 1; $x <= $this->config->param1Int('num_of_cells'); $x++) {
            $cellX[$x] = $x;
        }

        for ($y = 1; $y <= $this->config->param2Int('num_of_cells'); $y++) {
            $cellY[$y] = $y;
        }

        for ($y = 0; $y <= $this->config->param2Int('num_planets'); $y++) {
            $planet[$y] = $y;
        }

        $bookmark = new Bookmark();

        $form_new = $this->createFormBuilder($bookmark)
            ->add('sectorX', ChoiceType::class, [
                'choices'  => $sectorX,
                'mapped' => false
            ])
            ->add('sectorY', ChoiceType::class, [
                'choices'  => $sectorY,
                'mapped' => false
            ])
            ->add('cellX', ChoiceType::class, [
                'choices'  => $cellX,
                'mapped' => false
            ])
            ->add('cellY', ChoiceType::class, [
                'choices'  => $cellY,
                'mapped' => false
            ])
            ->add('pos', ChoiceType::class, [
                'choices'  => $planet,
                'mapped' => false
            ])
            ->add('comment', TextType::class, [
                'attr'  => [
                    'size' => 20,
                    'maxlength' => 200,
                    'placeholder' => 'Kommentar'
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Speichern'])
            ->getForm()
            ->handleRequest($request);

        if ($form_new->isSubmitted() && $form_new->isValid()) {
            $cx = intval($form_new->get('cellX')->getData());
            $sx = intval($form_new->get('sectorX')->getData());
            $sy = intval($form_new->get('sectorY')->getData());
            $cy = intval($form_new->get('cellY')->getData());
            $pos = intval($form_new->get('pos')->getData());

            $absX = (($sx - 1) * $this->config->param1Int('num_of_cells')) + $cx;
            $absY = (($sy - 1) * $this->config->param2Int('num_of_cells')) + $cy;
            if ($this->userUniverseDiscoveryService->discovered($user, $absX, $absY)) {
                $entity = $this->entityRepository->findByCoordinates(new EntityCoordinates($sx, $sy, $cx, $cy, $pos));
                if ($entity !== null) {
                    if (!$this->bookmarkRepository->findOneBy(['user'=>$user,'entity'=>$entity])) {
                        $bookmark->setUser($user);
                        $bookmark->setEntity($entity);

                        $this->bookmarkRepository->persist($bookmark);
                        $this->bookmarkRepository->save();

                        $msg['success'] = "Der Favorit wurde hinzugefügt!";
                    } else {
                        $msg['error'] = "Dieser Favorit existiert schon!";
                    }
                } else {
                    $msg['error'] = "Es existiert kein Objekt an den angegebenen Koordinaten!";
                }
            } else {
                $msg['error'] = "Das Gebiet ist noch nicht erkundet!";
            }
        }

        $sort = [];

        foreach (BookmarkOrder::ALL_ORDERS as $value => $name) {
            $sort[$name] = $value;
        }

        $form_sort = $this->createFormBuilder($properties)
            ->add('itemOrderBookmark', ChoiceType::class, [
                'choices'  => $sort,
                'data' => $properties->getItemOrderBookmark(),
                'constraints' => [
                    new AlphaDotsOrUnderlinesConstraint()
                ]
            ])
            ->add('itemOrderWay', ChoiceType::class, [
                'choices'  => [
                    'Aufsteigend' => 'ASC',
                    'Absteigend' => 'DESC'
                ],
                'data' => $properties->getItemOrderWay(),
                'constraints' => [
                    new AlphaDotsOrUnderlinesConstraint()
                ]
            ])

            ->add('save', SubmitType::class, ['label' => 'Sortieren'])
            ->getForm()
            ->handleRequest($request);

        if ($form_sort->isSubmitted() && $form_sort->isValid()) {
            $this->userRepository->save();
        }

        return $this->render('game/bookmarks/bookmarks_target.html.twig',[
            'form_new' => $form_new,
            'form_sort' => $form_sort,
            'bookmarks' => $bookmarks,
            'msg' => $msg??null
        ]);
    }

    #[Route('/game/bookmarks/fleet', name: 'game.bookmarks.fleet')]
    public function fleet(): Response {
        $user = $this->getUser()->getData();
        $bookmarks = $this->fleetBookmarkRepository->findBy(['user'=>$user],['name'=>'DESC']);

        if($bookmarks) {
            return $this->render('game/bookmarks/bookmarks_target.html.twig',[
                'bookmarks' => $bookmarks,
                'msg' => $msg??null
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Noch keine Favoriten vorhanden!',
            'path' => $this->generateUrl('game.bookmarks.new'),
            'headline' => 'Favoriten'
        ]);
    }

    #[Route('/game/bookmarks/new', name: 'game.bookmarks.new')]
    public function new(): Response {

    }

    #[Route('/game/bookmarks/edit/{id}', name: 'game.bookmarks.edit')]
    public function edit(Request $request, ?Bookmark $bookmark = null): Response {
        if($bookmark) {
            $form = $this->createFormBuilder($bookmark)
                ->add('comment', TextareaType::class, [
                    'attr' => [
                        'rows'=>3,
                        'cols'=>60
                    ],
                    'constraints' => [
                        new AlphaDotsOrUnderlinesConstraint()
                    ],
                ])
                ->add('save', SubmitType::class, ['label' => 'Speichern'])
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->bookmarkRepository->save();
                $msg['success'] = 'Gespeichert';
            }

            return $this->render('game/bookmarks/bookmarks_edit.html.twig',[
                'form' => $form,
                'msg' => $msg??null
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Datensatz nicht gefunden!',
            'path' => $this->generateUrl('game.bookmarks.target'),
            'headline' => 'Favoriten'
        ]);
    }

    #[Route('/game/bookmarks/delete/{id}', name: 'game.bookmarks.delete')]
    public function delete(Request $request, ?Bookmark $bookmark = null): Response {
        if($bookmark && $bookmark->getUser() === $this->getUser()->getData()) {
            $this->bookmarkRepository->remove($bookmark);
            $this->bookmarkRepository->save();

            return $this->render('game/success.html.twig',[
                'msg' => 'Gelöscht!',
                'path' => $this->generateUrl('game.bookmarks.target'),
                'headline' => 'Favoriten'
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Datensatz nicht gefunden!',
            'path' => $this->generateUrl('game.bookmarks.target'),
            'headline' => 'Favoriten'
        ]);
    }
}