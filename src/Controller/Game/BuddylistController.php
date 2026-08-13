<?php

namespace EtoA\Controller\Game;

use EtoA\BuddyList\BuddyListRepository;
use EtoA\Entity\Buddy;
use EtoA\Entity\User;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionLogRepository;
use EtoA\User\UserSessionRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuddylistController extends AbstractGameController
{
    public function __construct(
        private readonly BuddyListRepository $buddyListRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly UserSessionRepository $userSessionRepository,
        private readonly UserSessionLogRepository $userSessionLogRepository,
        private readonly UserRepository $userRepository,
        private readonly MessageRepository $messageRepository,
        private readonly MessageCategoryRepository $messageCategoryRepository
    )
    {
    }

    #[Route('/game/buddylist/overview', name: 'game.buddylist.overview')]
    public function overview(Request $request): Response
    {
        /** @var Buddy[] $buddies */
        $buddies = $this->buddyListRepository->findBy(['user'=>$this->getUser()->getData()]);

        /** @var Buddy[] $buddies */
        $pendingBuddies = $this->buddyListRepository->findBy(['allowed'=>0,'buddy'=>$this->getUser()->getData()]);

        $form = $this->createFormBuilder()
            ->add('user', TextType::class, [
                'label' => false,
                'attr' => [
                    'data-live-action-param'=>"getCryptoDistance",
                    'data-model' => 'debounce(500)|value',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Freund hinzufügen'
            ])
            ->getForm()
            ->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->userRepository->findOneBy(['nick'=>$form->get('user')->getData()]);
            $msg = $this->addRequest($user);
        }

        return $this->render('game/buddy/buddy_overview.html.twig',[
            'msg' => $msg??null,
            'form' => $form,
            'buddies' => $buddies,
            'pendings' => $pendingBuddies,
            'planetRepository' => $this->planetRepository,
            'sessionRepository' => $this->userSessionRepository,
            'sessionLogRepository' => $this->userSessionLogRepository
        ]);
    }

    #[Route('/game/buddylist/accept/{id}', name: 'game.buddylist.accept')]
    public function accept(Buddy $buddy): Response
    {
        if ($buddy->getBuddy() === $this->getUser()->getData() && $buddy->isAllowed() === false) {
            $this->buddyListRepository->acceptBuddyRequest($buddy);

            return $this->render('game/success.html.twig', [
                'msg' => 'Erlaubnis erteilt!',
                'path' => $this->generateUrl('game.buddylist.overview'),
                'headline' => 'Buddylist'
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Die Erlaubnis kann nicht erteilt werden weil die Anfrage gelöscht wurde!',
            'path' => $this->generateUrl('game.buddylist.overview'),
            'headline' => 'Buddylist'
        ]);
    }

    #[Route('/game/buddylist/deny/{id}', name: 'game.buddylist.deny')]
    public function deny(Buddy $buddy): Response
    {
        if ($buddy->getBuddy() === $this->getUser()->getData() && $buddy->isAllowed() === false) {
            $this->buddyListRepository->remove($buddy);
            $this->buddyListRepository->save();

            return $this->render('game/success.html.twig', [
                'msg' => 'Die Anfrage wurde gelöscht!',
                'path' => $this->generateUrl('game.buddylist.overview'),
                'headline' => 'Buddylist'
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Die Erlaubnis kann nicht erteilt werden weil die Anfrage gelöscht wurde!',
            'path' => $this->generateUrl('game.buddylist.overview'),
            'headline' => 'Buddylist'
        ]);

    }

    #[Route('/game/buddylist/remove/{id}', name: 'game.buddylist.remove')]
    public function remove(Buddy $buddy): Response
    {
        if ($buddy->getUser() === $this->getUser()->getData()) {
            $this->buddyListRepository->removeBuddy($buddy);

            return $this->render('game/success.html.twig', [
                'msg' => 'Der Spieler wurde von der Freundesliste entfern!',
                'path' => $this->generateUrl('game.buddylist.overview'),
                'headline' => 'Buddylist'
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Eintrag nicht gefunden!',
            'path' => $this->generateUrl('game.buddylist.overview'),
            'headline' => 'Buddylist'
        ]);
    }

    #[Route('/game/buddylist/comment/{id}', name: 'game.buddylist.comment')]
    public function comment(Request $request, ?Buddy $buddy = null): Response
    {
        if($buddy) {
            $form = $this->createFormBuilder($buddy)
                ->add('comment', TextareaType::class, [
                    'label' => false,
                    'attr' => [
                        'rows' => 5,
                        'cols' => 60
                    ]
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Speichern'
                ])
                ->getForm()
                ->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {
                $this->buddyListRepository->save();
                return $this->redirectToRoute('game.buddylist.overview');
            }

            return $this->render('game/buddy/buddy_comment.html.twig',[
                'msg' => $msg??null,
                'form' => $form,
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Daten nicht gefunden!',
            'path' => $this->generateUrl('game.buddylist.overview'),
            'headline' => 'Buddylist'
        ]);
    }

    #[Route('/game/buddylist/add/{id}', name: 'game.buddylist.add')]
    public function add(?User $user = null): Response
    {
        $msg = $this->addRequest($user);

        if(array_key_exists('success',$msg)) {
            return $this->render('game/success.html.twig', [
                'msg' => $msg['success'],
                'path' => $this->generateUrl('game.buddylist.overview'),
                'headline' => 'Buddylist'
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => $msg['error'],
            'path' => $this->generateUrl('game.buddylist.overview'),
            'headline' => 'Buddylist'
        ]);
    }

    private function addRequest(?User $user):array
    {
        if ($user) {
            $cu = $this->getUser()->getData();
            if ($cu !== $user) {
                if (!$this->buddyListRepository->findBy(['buddy'=>$user,'user'=>$this->getUser()->getData()])) {
                    $this->buddyListRepository->addBuddyRequest($cu, $user);
                    $this->messageRepository->createSystemMessage($user, $this->messageCategoryRepository->find(MessageCategoryId::MISC), "Buddylist-Anfrage von " . $cu->getNick(), "Der Spieler will dich zu seiner Freundesliste hinzuf&uuml;gen.\n\n[page=".$this->generateUrl('game.buddylist.overview')."]Anfrage bearbeiten[/page]");

                    $msg['success'] = "[b]" . $user->getNick() . "[/b] wurde zu deiner Liste hinzugefügt und ihm wurde eine Bestätigungsnachricht gesendet!";
                } else
                    $msg['error'] = "Dieser Eintrag ist schon vorhanden!";
            } else
                $msg['error'] = "Du kannst nicht dich selbst zur Buddyliste hinzufügen!";
        } else
            $msg['error'] = "Der Spieler konnte nicht gefunden werden!";

        return $msg;
    }
}