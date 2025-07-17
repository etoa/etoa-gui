<?php

namespace EtoA\Controller\Game;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Message;
use EtoA\Entity\MessageCategory;
use EtoA\Entity\MessageData;
use EtoA\Form\Type\Core\MessageDataType;
use EtoA\Form\Type\Core\MessageType;
use EtoA\Form\Validation\ValidUserConstraint;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageDataRepository;
use EtoA\Message\MessageIgnoreRepository;
use EtoA\Message\MessageRepository;
use EtoA\User\UserRepository;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessagesController extends AbstractGameController
{
    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly ConfigurationService $configurationService,
        private readonly MessageCategoryRepository $messageCategoryRepository,
        private readonly MessageIgnoreRepository $messageIgnoreRepository,
        private readonly UserRepository $userRepository,
        private readonly MessageDataRepository $messageDataRepository
    )
    {
    }

    #[Route('/game/messages/inbox', name: 'game.messages.inbox')]
    public function inbox(Request $request): Response {
        $readMessagesCount = $this->messageRepository->count(['read'=>true,'deleted'=>false,'archived'=>false,'userTo'=>$this->getUser()->getData()]);
        $archivedMessagesCount = $this->messageRepository->count(['deleted'=>false,'archived'=>true,'userTo'=>$this->getUser()->getData()]);

        // Rechnet %-Werte für tabelle (1/2)
        $percentRead = min(ceil($readMessagesCount / $this->configurationService->getInt('msg_max_store') * 100), 100);
        $percentArchived = min(ceil($archivedMessagesCount / $this->configurationService->param1Int('msg_max_store') * 100), 100);

        $r_color = ($percentRead >= 90) ? 'color:red;' : '';
        $a_color = ($percentArchived >= 90) ? 'color:red;' : '';

        /** @var MessageCategory[] $categories */
        $categories = $this->messageCategoryRepository->findBy([],['order'=>'ASC']);

        $categoriesWithMessages = [];

        foreach ($categories as $category) {
            $categoriesWithMessages[$category->getId()] = $this->messageRepository->findBy(['deleted'=>false,'archived'=>false,'userTo'=>$this->getUser()->getData(),'cat'=>$category]);
        }

        $form = $this->createFormBuilder($categoriesWithMessages);

        foreach ($categoriesWithMessages as $key => $value) {
            $form = $form->add($key, CollectionType::class, [
                'entry_type' => MessageType::class,
                'label' => false
            ]);
        }

        $form = $form
            ->add('delete', SubmitType::class, [
                'label' => 'Markierte löschen'
            ])
            ->add('deleteAll', SubmitType::class, [
                'label' => 'Alle löschen'
            ])
            ->add('deleteSystem', SubmitType::class, [
                'label' => 'Systemnachrichten löschen'
            ])
            ->add('archive', SubmitType::class, [
                'label' => 'Markierte archivieren'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('delete')->isClicked()) {
                $this->messageCategoryRepository->save();
            }

            if($form->get('deleteAll')->isClicked()) {
                foreach ($form->getData() as $data) {
                    foreach ($data as $message) {
                        $message->setDeleted(true);
                    }
                }
            }

            if($form->get('deleteSystem')->isClicked()) {
                foreach ($form->getData() as $data) {
                    foreach ($data as $message) {
                        if($message->getUserFrom() && $message->getUserFrom->getId() == 0) {
                            $message->setDeleted(true);
                        }
                    }
                }
            }

            if($form->get('archive')->isClicked()) {
                foreach ($form->getData() as $data) {
                    foreach ($data as $message) {
                        if($message->isDeleted()) {
                            $message->setDeleted(false);
                            $message->setArchived(true);

                        }
                    }
                }
            }
        }

        return $this->render('game/messages/inbox.html.twig', [
            'rColor' => $r_color,
            'aColor' => $a_color,
            'readMessagesCount' => $readMessagesCount,
            'archivedMessagesCount' => $archivedMessagesCount,
            'percentRead' => $percentRead,
            'percentArchived' => $percentArchived,
            'form' => $form,
            'messageCat' => $this->messageCategoryRepository
        ]);
    }

    #[Route('/game/messages/new', name: 'game.messages.new')]
    public function new(Request $request): Response {

        $session = $request->getSession();
        if(!$session->get('messagesSent'))
            $session->set('messagesSent',[]);

        if($this->getUser()->getData()->getVerificationKey()) {
            $message = new Message();
            $messageData = new MessageData();

            $message->setUserFrom($this->getUser()->getData());
            $message->setMessageData($messageData);

            $form = $this->createMessageForm($message);

            if ($form->isSubmitted() && $form->isValid()) {
                $flood_interval = time() - $this->configurationService->getInt('msg_flood_control');
                $session = $request->getSession();

                if (isset($session->get('messagesSent')[$this->getUser()->getId()]) && $session->get('messagesSent')[$this->getUser()->getId()] > $flood_interval) {
                    $msg['error'] = "<b>Flood-Kontrolle!</b> Du kannst erst nach ". $this->configurationService->getInt('msg_flood_control') . " Sekunden eine neue Nachricht an " . $message->getUserTo()->getNick() . " schreiben!<br/>";
                } else {
                    if ($this->messageIgnoreRepository->isRecipientIgnoringSender($message->getUserFrom(), $message->getUserTo())) {
                        $msg['error'] = "<b>Fehler:</b> Dieser Benutzer hat dich ignoriert, die Nachricht wurde nicht gesendet!<br/>";
                    }
                    else {
                        $session->set('messagesSent',[$this->getUser()->getId()=>time()]);

                        $message->setCat($cat??$this->messageCategoryRepository->find(MessageCategoryId::USER));
                        $message->setTimestamp(time());

                        $this->messageDataRepository->persist($messageData);
                        $this->messageDataRepository->save();

                        $this->messageRepository->persist($message);
                        $this->messageRepository->save();



                        $msg['success'] = "Nachricht wurde an <b>" . $message->getUserTo()->getNick() . "</b> gesendet!<br>";
                    }
                }
             }

            return $this->render('game/messages/new.html.twig', [
                'form' => $form,
                'msg' => $msg??null
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Solange deine E-Mail Adresse nicht bestätigt ist, kannst du keine Nachrichten versenden!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Nachrichten'
        ]);
    }

    #[Route('/game/messages/archive', name: 'game.messages.archive')]
    public function archive(Request $request): Response {

    }

    #[Route('/game/messages/sent', name: 'game.messages.sent')]
    public function sent(Request $request): Response {

    }

    #[Route('/game/messages/deleted', name: 'game.messages.deleted')]
    public function deleted(Request $request): Response {

    }

    #[Route('/game/messages/ignore', name: 'game.messages.ignore')]
    public function ignore(Request $request): Response {

    }

    private function createMessageForm(Message $message): FormInterface
    {
        $request = Request::createFromGlobals();

        $form = $this->createFormBuilder($message)
            ->add('userTo', TextType::class,[
                'mapped'=>false,
                'label' => false,
                'constraints' => [new ValidUserConstraint()],
                'error_bubbling' => true
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event): void {
                $form = $event->getForm();
                $form->getData()->setUserTo($this->userRepository->findOneBy(['nick'=>$event->getData()['userTo']]));
            })
            ->add('messageData', MessageDataType::class, [
                'label' => false
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Senden',

            ])
            ->getForm()
            ->handleRequest($request);

        return $form;
    }
}