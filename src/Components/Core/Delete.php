<?php

namespace EtoA\Components\Core;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/delete.html.twig')]
class Delete extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public bool $confirmation = false;
    #[LiveProp]
    public string $cancelDelete;
    #[LiveProp]
    public string $confirmDelete;
    #[LiveProp]
    public string $password;
    #[LiveProp]
    public string $userDeleted;
}