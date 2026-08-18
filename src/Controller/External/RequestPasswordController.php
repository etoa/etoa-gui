<?php

namespace EtoA\Controller\External;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\Checker;
use EtoA\User\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EtoA\Support\ValidationUtils;
use Symfony\Component\Validator\Constraints\NotBlank;

class RequestPasswordController extends AbstractController
{


    public function __construct(
        private readonly ConfigurationService $configurationService,
        private readonly UserService $userService,
    )
    {
    }

    #[Route('/request-password', name: 'external.request-password')]
    public function index(
        Request     $request,
    ): Response
    {
        $form = $this->createFormBuilder(options: ['attr'=>['class'=>'styled-form styled-form-medium']])
            ->add('nick', TextType::class, [
                'label' => false,
                'attr' => [
                    'maxlength' => $this->configurationService->param2('nick_length'),
                    'size' => 30,
                    'autofocus' => 1
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'maxlength' => 50,
                    'size' => 30,
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Passwort anfordern',
                'attr' => [
                    'class' => 'button'
                ]
            ])
            ->getForm()
            ->handleRequest($request);


        if ($form->isSubmitted()) {
            if($form->isValid()) {
                try {
                    $this->userService->resetPassword($form->get('nick')->getData(), $form->get('email')->getData());
                    $this->addFlash('success', 'Deine Passwort-Anfrage war erfolgreich. Du solltest in einigen Minuten eine E-Mail mit dem neuen Passwort erhalten!');
                    return $this->redirectToRoute('external.login');
                } catch (Exception $ex) {
                    $this->addFlash('error', $ex->getMessage());
                }
            }
            else {
                $this->addFlash('error', 'Du hast keinen Benutzernamen oder keine E-Mail-Adresse eingegeben oder ein unerlaubtes Zeichen verwendet!');
            }
        }

        return $this->render('external/pwforgot.html.twig', [
            'roundName' => $this->configurationService->get('roundname'),
            'form' => $form
        ]);
    }
}