<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Chat\ChatLogRepository;
use EtoA\Chat\ChatLogSearch;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Type\Admin\ChatLogSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('admin_chat_log_search')]
class ChatLogSearchComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use SearchComponentTrait;

    public function __construct(
        private ChatLogRepository $chatLogRepository,
    ) {
        $this->perPage = 250;
    }

    public function getSearch(): SearchResult
    {
        $search = ChatLogSearch::create();
        if ($this->getFormValues()['userId'] !== '') {
            $search->userId((int) $this->getFormValues()['userId']);
        }

        if ($this->getFormValues()['text'] !== '') {
            $search->textLike($this->getFormValues()['text']);
        }

        $total = $this->chatLogRepository->count($search);

        $limit = $this->getLimit($total);

        $entries = $this->chatLogRepository->search($search, $this->perPage, $limit);

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ChatLogSearchType::class, $this->getFormValues());
    }
}
