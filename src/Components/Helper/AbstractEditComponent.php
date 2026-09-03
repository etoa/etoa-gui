<?php declare(strict_types=1);

namespace EtoA\Components\Helper;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

abstract class AbstractEditComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public bool $isEdit = false;


    #[LiveAction]
    public function showEdit(): void
    {
        $this->resetFormValues();
        $this->isEdit = true;
    }

    #[LiveAction]
    public function abortEdit(): void
    {
        $this->resetFormValues();
        $this->isEdit = false;
    }

    #[LiveAction]
    public function submit(): void
    {
        $this->submitForm();

        $this->storeItem();
        $this->resetFormValues();
        $this->isEdit = false;
    }

    abstract protected function storeItem(): void;
    abstract public function getItem(): ?object;

    private function resetFormValues(): void
    {
        $this->resetForm();
    }
}
