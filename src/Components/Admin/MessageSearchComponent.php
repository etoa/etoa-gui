<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\MessageSearchRequest;
use EtoA\Form\Type\Admin\MessageSearchType;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Message\MessageSearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_message_search')]
class MessageSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $users;
    /** @var array<int, string> */
    public array $categories;
    private MessageSearchRequest $request;

    public function __construct(
        private MessageRepository $messageRepository,
        private MessageCategoryRepository $messageCategoryRepository,
        private UserRepository $userRepository
    ) {
        $this->request = new MessageSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = MessageSearch::create();
        if ($this->request->sender !== null) {
            $search->fromUser($this->request->sender);
        }

        if ($this->request->recipient !== null) {
            $search->toUser($this->request->recipient);
        }

        if ($this->request->subject !== null) {
            $search->subjectLike($this->request->subject);
        }

        if ($this->request->text !== null) {
            $search->textLike($this->request->text);
        }

        if ($this->request->category !== null) {
            $search->category($this->request->category);
        }

        if ($this->request->read !== null) {
            $search->read($this->request->read);
        }

        if ($this->request->deleted !== null) {
            $search->deleted($this->request->deleted);
        }

        if ($this->request->archived !== null) {
            $search->archived($this->request->archived);
        }

        if ($this->request->massmail !== null) {
            $search->massmail($this->request->massmail);
        }

        $total = $this->messageRepository->count($search);

        $limit = $this->getLimit($total);

        $logs = $this->messageRepository->search($search, $this->perPage, $limit);

        if ($total > 0) {
            $this->users = $this->userRepository->searchUserNicknames();
            $this->users[0] = 'System';
            $this->categories = $this->messageCategoryRepository->getNames();
        }

        return new SearchResult($logs, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(MessageSearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new MessageSearchRequest();
    }
}
