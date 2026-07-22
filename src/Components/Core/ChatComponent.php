<?php declare(strict_types=1);

namespace EtoA\Components\Core;

use EtoA\Chat\ChatBanRepository;
use EtoA\Chat\ChatManager;
use EtoA\Chat\ChatMessage;
use EtoA\Chat\ChatRepository;
use EtoA\Chat\ChatUserRepository;
use EtoA\Entity\ChatUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('chat_view', route: 'live_component_game')]
class ChatComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly ChatBanRepository  $chatBanRepository,
        private readonly ChatUserRepository $chatUserRepository,
        private readonly ChatRepository     $chatRepository,
        private readonly ChatManager        $chatManager,
        private readonly RequestStack       $requestStack
    )
    {
    }

    #[LiveProp(writable: true)]
    public string $data = '';

    #[LiveProp(writable: true)]
    public bool $showChat;

    #[LiveAction]
    public function send(): void
    {
        $this->chatManager->push($this->data);

        $this->data = '';

    }

    #[LiveAction]
    public function logout(): void
    {
        $this->chatManager->logout();
        $this->showChat = false;
    }

    #[LiveAction]
    public function default(): void
    {
        // Check user is logged in
        $user = $this->getUser()->getData();

        $chatUser = $this->chatUserRepository->getChatUser($user);
        if ($chatUser) {
            if ($chatUser->getKick()) {
                $this->chatManager->logout();
            }
        } else {
            // User does not exist yet
            $this->chatManager->sendSystemMessage($user->getNick() . ' betritt den Chat.');
            $data['cmd'] = 'li';
            $data['msg'] = $this->chatManager->getWelcomeMessage($user->getNick());
        }

        // User exists, not kicked, not banned.
        $this->chatManager->updateUserEntry($user);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createFormBuilder(options: ['attr' => ['autocomplete' => 'off']])
            ->add('message', TextType::class, [
                'attr' => [
                    'size' => 40,
                    'maxlength' => 255,
                    'data-model' => "data",
                    'style' => 'color:' . $this->getUser()->getData()->getUserProperties()->getChatColor(),
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Speichern',
                'attr' => [
                    'data-action' => 'live#action:prevent',
                    'data-live-action-param' => "send",
                    'style' => 'display:none'
                ]
            ])
            ->getForm();
    }


    /**
     * @return ChatMessage[]
     */
    public function getMessages(): array
    {
        return $this->chatRepository->getMessagesAfter(0);
    }

    /**
     * @return ChatUser[]
     */
    public function getUsers(): array
    {
        return $this->chatUserRepository->getChatUsers();
    }

    public function getBan(): bool
    {
        return !!$this->chatBanRepository->find($this->getUser()->getData());
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

    public function mount(): void
    {
        $session = $this->requestStack->getSession();

        $this->showChat = $session->has('chat') && $session->get('chat');
    }
}
