<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Components\Helper\SearchComponentTrait;
use EtoA\Components\Helper\SearchResult;
use EtoA\Form\Type\Admin\EntitySearchType;
use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('admin_entity_search')]
class EntitySearchComponent extends AbstractController
{
    use SearchComponentTrait;

    /** @var array<int, string> */
    public array $solarTypes;
    /** @var array<int, string> */
    public array $planetTypes;

    public function __construct(
        private EntityRepository $entityRepository,
        private PlanetTypeRepository $planetTypeRepository,
        private SolarTypeRepository $solarTypeRepository
    ) {
    }

    public function getSearch(): SearchResult
    {
        $search = EntityLabelSearch::create();
        if ($this->getFormValues()['name'] !== '') {
            $search->likePlanetName($this->getFormValues()['name']);
        }

        if ($this->getFormValues()['entity'] !== '') {
            $search->id((int) $this->getFormValues()['entity']);
        }

        if ($this->getFormValues()['cell'] !== '') {
            $search->cellId((int) $this->getFormValues()['cell']);
        }

        if ($this->getFormValues()['user'] !== '') {
            $search->planetUserId((int) $this->getFormValues()['user']);
        }

        if ($this->getFormValues()['code'] !== '') {
            $search->codeIn([$this->getFormValues()['code']]);
        }

        if (!is_array($this->getFormValues()['isMainPlanet']) && $this->getFormValues()['isMainPlanet'] !== '') {
            $search->planetUserMain((bool) $this->getFormValues()['isMainPlanet']);
        }

        if (!is_array($this->getFormValues()['planetDebris']) && $this->getFormValues()['planetDebris'] !== '') {
            $search->planetDebris((bool) $this->getFormValues()['planetDebris']);
        }

        $total = $this->entityRepository->countEntityLabels($search);

        $limit = $this->getLimit($total);

        $entities = $this->entityRepository->searchEntityLabels($search, null, $this->perPage, $limit);

        if ($total > 0) {
            $this->planetTypes = $this->planetTypeRepository->getPlanetTypeNames(true);
            $this->solarTypes = $this->solarTypeRepository->getSolarTypeNames(true);
        }

        return new SearchResult($entities, $limit, $total, $this->perPage);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EntitySearchType::class, $this->getFormValues());
    }

    private function resetFormValues(): void
    {
        $this->formValues = [];
        foreach ($this->getFormInstance()->all() as $field) {
            $this->formValues[$field->getName()] = '';
        }
    }
}
