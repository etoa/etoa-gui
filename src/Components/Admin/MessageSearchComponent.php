<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Type\Admin\MessageSearchType;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Message\MessageSearch;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('admin_message_search')]
class MessageSearchComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $users;
    /** @var array<int, string> */
    public array $categories;

    public function __construct(
        private MessageRepository $messageRepository,
        private MessageCategoryRepository $messageCategoryRepository,
        private UserRepository $userRepository
    ) {
    }

    public function getSearch(): SearchResult
    {
        $search = MessageSearch::create();
        if ($this->getFormValues()['sender'] !== '') {
            $search->fromUser((int) $this->getFormValues()['sender']);
        }

        if ($this->getFormValues()['recipient'] !== '') {
            $search->toUser((int) $this->getFormValues()['recipient']);
        }

        if ($this->getFormValues()['subject'] !== '') {
            $search->subjectLike($this->getFormValues()['subject']);
        }

        if ($this->getFormValues()['text'] !== '') {
            $search->textLike($this->getFormValues()['text']);
        }

        if ($this->getFormValues()['category'] !== '') {
            $search->category((int) $this->getFormValues()['category']);
        }

        if (!is_array($this->getFormValues()['read']) && $this->getFormValues()['read'] !== '') {
            $search->read((bool) $this->getFormValues()['read']);
        }

        if (!is_array($this->getFormValues()['deleted']) && $this->getFormValues()['deleted'] !== '') {
            $search->deleted((bool) $this->getFormValues()['deleted']);
        }

        if (!is_array($this->getFormValues()['archived']) && $this->getFormValues()['archived'] !== '') {
            $search->archived((bool) $this->getFormValues()['archived']);
        }

        if (!is_array($this->getFormValues()['massmail']) && $this->getFormValues()['massmail'] !== '') {
            $search->massmail((bool) $this->getFormValues()['massmail']);
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
        return $this->createForm(MessageSearchType::class, $this->getFormValues());
    }

    private function resetFormValues(): void
    {
        $this->formValues = [];
        foreach ($this->getFormInstance()->all() as $field) {
            $this->formValues[$field->getName()] = '';
        }
    }
}
