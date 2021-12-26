<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceSearch;
use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Type\Admin\AllianceSearchType;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

#[AsLiveComponent('alliance_search')]
class AllianceSearchComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use SearchComponentTrait;
    /** @var string[] */
    public array $users = [];

    public function __construct(
        private UserRepository $userRepository,
        private AllianceRepository $allianceRepository
    ) {
        $this->perPage = 99999;
    }

    public function getSearch(): SearchResult
    {
        $search = AllianceSearch::create();
        if ($this->getFormValues()['name'] !== '') {
            $search->nameLike($this->getFormValues()['name']);
        }

        if ($this->getFormValues()['tag'] !== '') {
            $search->nameLike($this->getFormValues()['tag']);
        }

        if ($this->getFormValues()['text'] !== '') {
            $search->nameLike($this->getFormValues()['text']);
        }

        $alliances = $this->allianceRepository->searchAlliances($search);
        if (count($alliances) > 0) {
            $this->users = $this->userRepository->searchUserNicknames();
        }

        return new SearchResult($alliances, 0, count($alliances), $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(AllianceSearchType::class, $this->getFormValues());
    }

    private function resetFormValues(): void
    {
        $this->formValues = [
            'text' => '',
            'tag' => '',
            'name' => '',
        ];
    }
}
