<?php

namespace EtoA\Controller\External;

use EtoA\Core\AppName;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\User;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\ExternalUrl;
use EtoA\Support\Mail\MailSenderService;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use EtoA\Support\ValidationUtils;

class RegisterController extends AbstractController
{


    protected ?string $pageTitle = 'Registrieren';

    public function __construct(
        private readonly ConfigurationService $configurationService,
        private readonly UserRepository       $userRepository,
        private readonly LogRepository        $logRepository,
        private readonly UserService          $userService,
        private readonly MailSenderService    $mailSenderService,
    )
    {
    }

    #[Route('/register', name: 'external.register')]
    public function index(Request $request): Response
    {
        // Load user count
        $userCount = $this->userRepository->count([]);
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'maxlength' => $this->configurationService->getInt('name_length'),
                    'size' => $this->configurationService->getInt('name_length'),
                    'autocomplete' => "off",
                    'autofocus' => 1
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'maxlength' => 50,
                    'size' => 30,
                    'autocomplete' => "off",
                ]
            ])
            ->add('nick', TextType::class, [
                'label' => false,
                'attr' => [
                    'maxlength' => $this->configurationService->param2('nick_length'),
                    'size' => $this->configurationService->param2('nick_length'),
                    'autocomplete' => "off"
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => false,
                'attr' => [
                    'size' => 20,
                    'autocomplete' => "new-password",
                ],
                // the form hashes the input and writes it to User::setPassword()
                'hash_property_path' => 'password',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => $this->configurationService->getInt('password_minlength')]),
                ],
            ])
            ->add('agb', CheckboxType::class, [
                'label' => false,
                'mapped' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Anmelden!',
                'attr' => [
                    'disabled' => true,
                ]
            ])
            ->getForm()
            ->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            //
            // Handle registration submit
            //
            if ($this->configurationService->getBoolean('enable_register')) {
                try {
                    $newUser = $this->userService->register(
                        name: $user->getName(),
                        email: $user->getEmail(),
                        nick: $user->getNick(),
                        hashedPassword: $user->getPassword(),
                    );
                    $this->logRepository->add(
                        LogFacility::USER,
                        LogSeverity::INFO,
                        "Der Benutzer " . $newUser->getNick() . " (" . $newUser->getName() . ", " . $newUser->getEmail() . ") hat sich registriert!"
                    );

                    $verificationRequired = ValidationUtils::filled($newUser->getVerificationKey());
                    $verificationUrl = $verificationRequired
                        ? $this->configurationService->get('roundurl') . $this->generateUrl('external.verify-email', [
                            'key' => $newUser->getVerificationKey()
                        ])
                        : null;

                    $emailText = $this->render('email/register.txt.twig', [
                        'newUser' => $newUser,
                        'roundName' => $this->configurationService->get('roundname'),
                        'verificationUrl' => $verificationUrl,
                        'rulesUrl' => ExternalUrl::RULES,
                    ]);

                    $this->mailSenderService->send("Account-Registrierung", $emailText, $newUser->getEmail());

                    return $this->render('external/register-success.html.twig', [
                        'registerEmail' => $user->getEmail(),
                        'verificationRequired' => $verificationRequired,
                    ]);
                } catch (Exception $e) {
                    $this->addFlash('error', 'Die Registration hat leider nicht geklappt: ' . $e->getMessage());
                }
            }
        }

        $registrationLater = ($this->configurationService->getBoolean('enable_register') && $this->configurationService->param1Int('enable_register') > time())
            ? $this->configurationService->param1Int('enable_register')
            : null;
        return $this->render('external/register.html.twig',
            [
                'maxPlayerCount' => $userCount,
                'registrationNotEnabled' => !$this->configurationService->getBoolean('enable_register'),
                'registrationLater' => $registrationLater,
                'registrationFull' => $this->configurationService->param2Int('enable_register') <= $userCount,
                'roundName' => $this->configurationService->get('roundname'),
                'appName' => AppName::NAME,
                'rulesUrl' => ExternalUrl::RULES,
                'privacyUrl' => ExternalUrl::PRIVACY,
                'form' => $form
            ]
        );
    }
}