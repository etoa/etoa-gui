<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Chat\ChatLogRepository;
use EtoA\Chat\ChatLogSearch;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Request\Admin\ChatLogSearchRequest;
use EtoA\Form\Type\Admin\ChatLogSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_chat_log_search')]
class ChatLogSearchComponent extends AbstractController
{
    use SearchComponentTrait;

    private ChatLogSearchRequest $request;

    public function __construct(
        private ChatLogRepository $chatLogRepository,
    ) {
        $this->perPage = 250;
        $this->request = new ChatLogSearchRequest();
    }

    public function getSearch(): SearchResult
    {
        $search = ChatLogSearch::create();
        if ($this->request->userId !== null) {
            $search->userId($this->request->userId);
        }

        if ($this->request->text !== null) {
            $search->textLike($this->request->text);
        }

        $total = $this->chatLogRepository->count($search);

        $limit = $this->getLimit($total);

        $entries = $this->chatLogRepository->search($search, $this->perPage, $limit);

        return new SearchResult($entries, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ChatLogSearchType::class, $this->request);
    }

    private function resetFormRequest(): void
    {
        $this->request = new ChatLogSearchRequest();
    }
}
