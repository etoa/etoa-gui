<?php

namespace EtoA\Components\Core;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Form\Validation\SamePasswordConstraint;
use EtoA\Support\StringUtils;
use EtoA\User\UserHolidayService;
use EtoA\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(template: 'components/delete.html.twig',route: 'live_component_game')]
class Delete extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public function __construct(
        private readonly UserService          $userService,
        private readonly UserHolidayService   $userHolidayService,
        private readonly ConfigurationService $config,
    )
    {
    }

    #[LiveProp(writable: true)]
    public bool $confirmation = false;

    #[LiveAction]
    public function cancelDelete(): RedirectResponse
    {
        $user = $this->getUser()->getData();
        $this->userService->updateDelete($user, 0);
        $this->userService->addToUserLog($user, "settings", "{nick} hat seine Accountlöschung aufgehoben.", );
        $this->addFlash('success', 'Löschantrag aufgehoben!');
        return $this->redirectToRoute('game.config.misc');
    }

    #[LiveAction]
    public function confirmDelete(): RedirectResponse
    {
        $this->submitForm();

        $user = $this->getUser()->getData();
        $timestamp = time() + ($this->config->getInt('user_delete_days') * 3600 * 24);
        $this->userService->updateDelete($user, $timestamp);
        $this->userHolidayService->activateHolidayMode($user, true);
        $this->userService->addToUserLog($user, "settings", "{nick} hat seinen Account zur Löschung freigegeben.", true);
        $this->addFlash('success', "Deine Daten werden am " . StringUtils::formatDate(time() + ($this->config->getInt('user_delete_days') * 3600 * 24)) . " Uhr von unserem System gelöscht! Wir wünschen weiterhin viel Erfolg im Netz!");

        return $this->redirectToRoute('game.config.misc');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->add('cancelDelete', ButtonType::class, [
                'label' => 'Löschantrag aufheben',
                'attr' => [
                    'style' => 'color:#0f0',
                    'data-action'=>"live#action:render",
                    'data-live-action-param'=>"cancelDelete"
                ]
            ])
            ->add('confirmDelete', ButtonType::class, [
                'label' => 'Account löschen',
                'attr' => [
                    'data-action'=>"live#action:update",
                    'data-live-action-param'=>"confirmDelete"
                ]
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank(message: 'Passwort darf nicht leer sein'),
                    new SamePasswordConstraint('Falsches Passwort!')
                ],
                'empty_data' => '',
                'always_empty' => false,
            ])
            ->getForm();
    }

    #[ExposeInTemplate]
    public function isUserDeleted(): bool
    {
        return (bool)$this->getUser()->getData()->getDeleted();
    }
}