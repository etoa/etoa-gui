<?php

namespace EtoA\Components\Core;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use EtoA\Form\Type\Core\MultiViewType;
use EtoA\User\UserMulti;

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