<?php declare(strict_types=1);

namespace EtoA\Components\Helper;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;

abstract class AbstractEditComponent extends AbstractController
{
    use ComponentWithFormTrait;

    #[LiveProp]
    public bool $isEdit = false;


    #[LiveAction]
    public function showEdit(): void
    {
        $this->isEdit = true;
    }

    #[LiveAction]
    public function abortEdit(): void
    {
        $this->isEdit = false;
    }

    #[LiveAction]
    public function submit(): void
    {
        $this->submitForm();

        $this->storeItem();
        $this->isEdit = false;
    }

    abstract protected function storeItem(): void;
}
