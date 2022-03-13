<?php declare(strict_types=1);

namespace EtoA\Components\Helper;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

trait SearchComponentTrait
{
    use ComponentWithFormTrait;

    private int $perPage = 100;

    #[LiveProp(writable: true)]
    public int $limit = 0;

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    #[PostMount(priority: 255)]
    public function initializeSearchForm(array $data): array
    {
        if (isset($data['form']) && $data['form'] instanceof FormInterface) {
            $form = $data['form'];
            $data['form'] = $form->createView();

            // @phpstan-ignore-next-line
            if (property_exists($this, 'request')) {
                $propertyAccessor = new PropertyAccessor();
                $children = $form->all();
                $formData = $form->getData();
                if (is_array($formData)) {
                    foreach ($formData as $key => $value) {
                        if ($value !== null) {
                            // @phpstan-ignore-next-line
                            foreach ($children[$key]->getConfig()->getViewTransformers() as $transformer) {
                                $value = $transformer->reverseTransform($value);
                            }
                            $propertyAccessor->setValue($this->request, (string) $key, $value);
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * New search terms need to reset the current pagination progress
     */
    public function __invoke(): void
    {
        $this->limit = 0;
    }


    #[LiveAction]
    public function firstPage(): void
    {
        $this->limit = 0;
    }

    #[LiveAction]
    public function nextPage(): void
    {
        $this->limit += $this->perPage;
    }

    #[LiveAction]
    public function previousPage(): void
    {
        $this->limit -= $this->perPage;
    }

    #[LiveAction]
    public function lastPage(): void
    {
        $this->limit = PHP_INT_MAX;
    }

    #[LiveAction]
    public function reset(): void
    {
        $this->limit = 0;
        $this->formValues = [];
        $this->resetFormRequest();
        $this->instantiateForm();
    }

    private function getLimit(int $total): int
    {
        $limit = max(0, $this->limit);
        $limit = min($total, $limit);
        $limit -= $limit % $this->perPage;

        $this->limit = $limit;

        return $limit;
    }

    abstract public function getSearch(): SearchResult;
}
