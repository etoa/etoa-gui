<?php

namespace EtoA\Controller\Game;

use EtoA\Admin\AllianceBoardAvatar;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\User;
use EtoA\Entity\UserSitting;
use EtoA\Form\Type\Core\AvatarUploadType;
use EtoA\Form\Type\Core\DesignType;
use EtoA\Form\Type\Core\MultiViewType;
use EtoA\Form\Type\Core\ProfileUploadType;
use EtoA\Form\Validation\NotSamePasswordConstraint;
use EtoA\Form\Validation\SamePasswordConstraint;
use EtoA\Form\Validation\ValidUserConstraint;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ranking\UserBannerService;
use EtoA\Ship\ShipDataRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\FileUtils;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Support\StringUtils;
use EtoA\User\ProfileImage;
use EtoA\User\UserHolidayService;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use EtoA\User\UserSessionLogRepository;
use EtoA\User\UserSessionRepository;
use EtoA\User\UserSessionSearch;
use EtoA\User\UserSittingRepository;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Symfony\Component\Clock\now;

class UserConfigController extends AbstractGameController
{
    public function __construct(
        private readonly UserRepository           $userRepository,
        private readonly MailSenderService        $mailSenderService,
        private readonly FileUtils                $fileUtils,
        private readonly ShipDataRepository       $shipDataRepository,
        private readonly UserPropertiesRepository $userPropertiesRepository,
    )
    {
    }

    #[Route('/game/config/general', name: 'game.config.general')]
    public function general(Request $request): Response
    {
        $user = $this->getUser()->getData();
        $msg['error'] = '';
        $msg['success'] = [];

        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class,
                [
                    'constraints' => new Email(
                        ['message' => 'Die E-Mail-Adresse ist nicht korrekt!']
                    ),
                ])
            ->add('profileText', TextareaType::class, ['required' => false])
            ->add('signature', TextareaType::class, ['required' => false])
            ->add('profileImage', ProfileUploadType::class)
            ->add('profileImgDel', CheckboxType::class, ['mapped' => false, 'required' => false])
            ->add('avatarDel', CheckboxType::class, ['mapped' => false, 'required' => false])
            ->add('profileBoardUrl', TextType::class, ['required' => false])
            ->add('avatar', AvatarUploadType::class)
            ->add('save', SubmitType::class, ['label' => 'Übernehmen'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Avatar
            if ($form->get('avatarDel')->getData()) {
                $user->setAvatar("");
            } elseif ($form->get('avatar')->getData()) {
                if ($file = $this->fileUtils->uploadImage(
                    $form->get('avatar')->getData(),
                    $this->getParameter('kernel.project_dir') . AllianceBoardAvatar::IMAGE_PATH,
                    [AllianceBoardAvatar::AVATAR_WIDTH, AllianceBoardAvatar::AVATAR_HEIGHT],
                    $msg['error']
                )) {
                    $user->setAvatar($file->getFilename());
                }
            }

            // Profil-Bild
            if ($form->get('profileImgDel')->getData()) {
                $user->setProfileImage("");
            } elseif ($form->get('profileImage')->getData()) {
                if ($file = $this->fileUtils->uploadImage(
                    $form->get('profileImage')->getData(),
                    $this->getParameter('kernel.project_dir') . ProfileImage::IMAGE_PATH,
                    [ProfileImage::IMAGE_WIDTH, ProfileImage::IMAGE_HEIGHT],
                    $msg['error']
                )) {
                    $user->setProfileImage($file->getFilename());
                }
            }

            //check if mail was changed
            $changeset = $this->userRepository->getChangeset($user);
            if (array_key_exists('email',$changeset)) {
                $subject = "Änderung deiner E-Mail-Adresse";
                $text = "Die E-Mail-Adresse deines Accounts " . $user->getNick() . " wurde von " . $changeset['email'][0] . " auf " . $changeset['email'][1] . " geändert!";
                $this->mailSenderService->send($subject, $text, $user->getEmail());
                if ($user->getEmailFix() !== $user->getEmail()) {
                    $this->mailSenderService->send($subject, $text, $user->getEmailFix());
                }
            }

            $this->userRepository->save();
            $msg['success'][] = "Benutzer-Daten wurden geändert!";
        }

        return $this->render('game/userconfig/general.html.twig', [
            'msg' => $msg,
            'form' => $form,
            'imgMax' => StringUtils::formatBytes(ProfileImage::IMAGE_MAX_SIZE),
            'avatarMax' => StringUtils::formatBytes(AllianceBoardAvatar::AVATAR_MAX_SIZE),
            'profileImg' => $user->getProfileImage() ? ProfileImage::IMAGE_PATH . $user->getProfileImage() : null,
            'allianceAvatar' => $user->getAvatar() && $user->getAvatar() != AllianceBoardAvatar::DEFAULT_IMAGE ? AllianceBoardAvatar::IMAGE_PATH . $user->getAvatar() : null,
        ]);
    }

    #[Route('/game/config/game', name: 'game.config.game')]
    public function game(Request $request): Response
    {
        $properties = $this->userPropertiesRepository->find($this->getUser()->getId());

        $form = $this->createFormBuilder($properties)
            ->add('spyShipCount', IntegerType::class, [
                'attr' => ['maxlength' => 5, 'size' => 5]
            ])
            ->add('spyShip', ChoiceType::class, [
                    'placeholder' => '(keines)',
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'choices' => $this->shipDataRepository->getShipsWithAction('spy'),
                    'required' => false
                ]
            )
            ->add('analyzeShipCount', IntegerType::class, [
                'attr' => ['maxlength' => 5, 'size' => 5]
            ])
            ->add('analyzeShip', ChoiceType::class, [
                'placeholder' => '(keines)',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choices' => $this->shipDataRepository->getShipsWithAction('analyze'),
                'required' => false
            ])
            ->add('exploreShipCount', IntegerType::class, [
                'attr' => ['maxlength' => 5, 'size' => 5]
            ])
            ->add('exploreShip', ChoiceType::class, [
                'placeholder' => '(keines)',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choices' => $this->shipDataRepository->getShipsWithAction('explore'),
                'required' => false
            ])
            ->add('startUpChat', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('showCellreports', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('showTut', ButtonType::class, ['label' => 'Anzeigen', 'attr' => ['onClick' => 'showTutorialText(1,0']])
            ->add('chatColor', ColorType::class, ['attr' =>
                [
                    'size' => 6,
                    'onkeyup' => "addFontColor(this.id,'chatPreview')",
                    'onchange' => "addFontColor(this.id,'chatPreview')",
                ]
            ])
            ->add('enableKeybinds', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'Übernehmen'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($properties->getChatColor() === '000' || $properties->getChatColor() === '000000') {
                $msg['success'] = 'Chatfarbe schwarz auf schwarz ist eine Weile ja ganz lustig, aber in ein paar Minuten bitte zurückändern';
            } else {
                $msg['success'] = 'Benutzer-Daten wurden geändert!';
            }
            $this->userPropertiesRepository->save();
        }

        return $this->render('game/userconfig/game.html.twig', [
            'form' => $form,
            'msg' => $msg ?? null,
            'chatColor' => $properties->getChatColor()
        ]);
    }

    #[Route('/game/config/messages', name: 'game.config.messages')]
    public function messages(Request $request): Response
    {
        $properties = $this->userPropertiesRepository->find($this->getUser()->getId());
        $form = $this->createFormBuilder($properties)
            ->add('msgSignature', TextareaType::class, [
                'attr' => ['cols' => 50, 'rows' => 4]
            ])
            ->add('msgPreview', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('msgCreationPreview', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('msgBlink', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('msgCopy', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('fleetRtnMsg', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'Übernehmen'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userPropertiesRepository->save();
            $msg['success'] = 'Nachrichten-Einstellungen wurden geändert!';
        }

        return $this->render('game/userconfig/messages.html.twig', [
            'form' => $form,
            'msg' => $msg ?? null,
        ]);
    }

    #[Route('/game/config/design', name: 'game.config.design')]
    public function design(Request $request): Response
    {
        $properties = $this->userPropertiesRepository->getOrCreateProperties($this->getUser()->getData());
        $planetWidth = [];

        for ($x = 450; $x <= 700; $x += 50) {
            $planetWidth[$x] = $x;
        }

        $form = $this->createFormBuilder($properties)
            ->add('cssStyle', DesignType::class)
            ->add('planetCircleWidth', ChoiceType::class, [
                'choices' => $planetWidth,
            ])
            ->add('itemShow', ChoiceType::class, [
                'choices' => [
                    ' Volle Ansicht ' => 'full',
                    ' Einfache Ansicht ' => 'small',
                ],
                'expanded' => true,
            ])
            ->add('imageFilter', ChoiceType::class, [
                'choices' => [
                    ' An ' => 1,
                    ' Aus ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('showAdds', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('smallResBox', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'Übernehmen'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userPropertiesRepository->save();
            $msg['success'] = 'Design-Daten wurden geändert!';
        }

        return $this->render('game/userconfig/design.html.twig', [
            'form' => $form,
            'msg' => $msg ?? null,
        ]);
    }

    #[Route('/game/config/sitting', name: 'game.config.sitting')]
    public function sitting(Request $request, UserSittingRepository $userSittingRepository): Response
    {
        $user = $this->getUser()->getData();

        $formBuilder = $this->createFormBuilder($user)
            ->add('save', SubmitType::class, ['label' => 'Übernehmen'])
            ->add('userMultis', CollectionType::class, array(
                'entry_type' => MultiViewType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ));

        $formBuilder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (PreSetDataEvent $event) {
                $data = $event->getData();
                if ($data instanceof User) {
                    $activeMultis = $data->getUserMultis()->filter(fn($multi) => $multi->isActive());
                    $data->getUserMultis()->clear();
                    foreach ($activeMultis as $multi) {
                        $data->getUserMultis()->add($multi);
                    }
                }
            }
        );

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->save();
        }

        if ($request->get('remove_sitting') && intval($request->get('remove_sitting')) > 0) {
            $success = $userSittingRepository->deleteFutureUserEntry((int)$request->get('remove_sitting'), $this->getUser()->getId());
            if ($success)
                $msg['success'] = "Sitting entfernt!";
        }
        if ($request->get('cancel_sitting') && intval($request->get('cancel_sitting')) > 0) {
            $success = $userSittingRepository->cancelUserEntry((int)$request->get('cancel_sitting'), $this->getUser()->getId());
            if ($success)
                $msg['success'] = "Sitting entfernt!";
        }

        return $this->render('game/userconfig/sitting.html.twig', [
            'form' => $form,
            'msg' => $msg ?? null,
            'sittingEntries' => $userSittingRepository->getWhereUser($this->getUser()->getId())
        ]);
    }

    #[Route('/game/config/sitting/new', name: 'game.config.sitting.new')]
    public function sittingAdd(Request $request, UserSittingRepository $userSittingRepository, ConfigurationService $config): Response
    {
        $sittingDays = max(0, $this->getUser()->getData()->getSittingDays() - $userSittingRepository->getUsedSittingTime($this->getUser()->getId()));
        $sittingLeft = [];

        if (!$sittingDays) {
            $msg['error'] = 'Alle Sitting-Tage sind aufgebraucht!';
            return $this->render('game/userconfig/sitting_add.html.twig', [
                'msg' => $msg
            ]);
        }

        for ($x = 1; $sittingDays >= $x; $x++) {
            $sittingLeft[$x] = $x;
        }

        $userSitting = new UserSitting();
        $userSitting->setUser($this->getUser()->getData());

        $form = $this->createFormBuilder($userSitting)
            ->add('save', SubmitType::class, ['label' => 'Speichern'])
            ->add('sitterNick', TextType::class, [
                'attr' => [
                    'autocomplete' => 'off',
                    'maxlength' => "20",
                    'size' => "20",
                ],
                'mapped' => false,
                'constraints' => [
                    new ValidUserConstraint(),
                    new NotBlank(['message' => 'Kein Name angegeben!']),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Passwörter sind nicht gleich oder zu kurz (mind. %min% Zeichen)',
                'invalid_message_parameters' => ['%min%' => $config->getInt('password_minlength')],
                'options' => [
                    'attr' => [
                        'autocomplete' => 'off',
                        'maxlength' => "20",
                        'size' => "20",
                        'minlength' => $config->getInt('password_minlength'),
                    ]
                ],
                'required' => true,
                'constraints' => [
                    new Length(['min' => $config->getInt('password_minlength')])
                ],
                'first_options' => [
                    'hash_property_path' => 'password',
                    'constraints' => [
                        new NotSamePasswordConstraint('Das Passwort darf nicht dasselbe wie das normale Accountpasswort sein!')
                    ],
                ],
                'mapped' => false,
            ])
            ->add('dateFrom', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['min' => now()->format("Y-m-d H:i")],
                'data' => now()->getTimestamp(),
                'input' => 'timestamp'
            ])
            ->add('dateToDays', ChoiceType::class, [
                'choices' => $sittingLeft,
                'mapped' => false
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tm_from = $form->getData()->getDateFrom();
            $tm_to = $tm_from + $form->get('dateToDays')->getData() * 86400;

            if ($tm_from > time() - 600 && $tm_from < $tm_to && $form->get('dateToDays')->getData() <= $sittingDays) {
                if (!$userSittingRepository->hasSittingEntryForTimeSpan($this->getUser()->getId(), $tm_from, $tm_to)) {
                    $sitterUser = $this->userRepository->getUserByNick($form->get('sitterNick')->getData());
                    $userSitting->setSitter($sitterUser);
                    $userSitting->setDateTo($tm_to);
                    $userSittingRepository->addEntry($userSitting);
                    $msg['success'] = "Sitting eingerichtet!";

                    return $this->render('game/userconfig/sitting_add.html.twig', [
                        'msg' => $msg
                    ]);
                } else {
                    $msg['error'] = "In diesem Zeitraum existiert bereits ein Sittingeintrag!";
                }
            } else {
                $msg['error'] = "Ungültiger Zeitraum!";
            }
        }

        return $this->render('game/userconfig/sitting_add.html.twig', [
            'form' => $form,
            'msg' => $msg ?? null
        ]);
    }

    #[Route('/game/config/dual', name: 'game.config.dual')]
    public function dual(Request $request): Response
    {

        $user = $this->getUser()->getData();
        $formInterface = $this->createFormBuilder($user);
        $formInterface->add('dualName', TextType::class, [
            'required' => false,
            'attr' => ['maxlength' => "255", 'size' => "30"],
            'constraints' => [
                new NotBlank([
                    'message' => 'Es ist kein Name angegeben!',
                ])
            ],
        ]);

        $formInterface->add('dualEmail', EMailType::class, [
            'required' => false,
            'attr' => ['maxlength' => "255", 'size' => "30"],
            'constraints' => [
                new Email([
                    'message' => 'Die E-Mail-Adresse ist nicht korrekt!',
                ])
            ],
        ]);

        $form = $formInterface->add('save', SubmitType::class, ['label' => 'Übernehmen'])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->save();
        }

        return $this->render('game/userconfig/dual.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/game/config/password', name: 'game.config.password')]
    public function password(Request $request, ConfigurationService $config, LogRepository $logRepository, UserService $userService): Response
    {
        $user = $this->getUser()->getData();
        $form = $this->createFormBuilder($user)
            ->add('save', SubmitType::class, ['label' => 'Speichern'])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Passwörter sind nicht gleich oder zu kurz (mind. %min% Zeichen)',
                'invalid_message_parameters' => ['%min%' => $config->getInt('password_minlength')],
                'options' => [
                    'attr' => [
                        'autocomplete' => 'off',
                        'maxlength' => "255",
                        'size' => "20",
                        'minlength' => $config->getInt('password_minlength'),
                    ]
                ],
                'required' => true,
                'constraints' => [
                    new Length(['min' => $config->getInt('password_minlength')])
                ],
                'first_options' => [
                    'hash_property_path' => 'password',
                    'constraints' => [
                        new NotSamePasswordConstraint()
                    ],
                ],
                'mapped' => false,
            ])
            ->add('oldPassword', PasswordType::class, [
                'constraints' => [
                    new SamePasswordConstraint('Dein altes Passwort stimmt nicht mit dem gespeicherten Passwort überein!')
                ],
                'mapped' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->save();
            $logRepository->add(LogFacility::USER, LogSeverity::INFO, "Der Spieler [b]" . $user->getNick() . "[/b] ändert sein Passwort!");

            $this->mailSenderService->send(
                "Passwortänderung",
                "Hallo " . $user->getNick() . "\n\nDies ist eine Bestätigung, dass du dein Passwort für deinen Account erfolgreich geändert hast!\n\nSolltest du dein Passwort nicht selbst geändert haben, so nimm bitte sobald wie möglich Kontakt mit einem Game-Administrator auf: https://www.etoa.ch/kontakt",
                $user->getEmail()
            );

            $userService->addToUserLog($user, "settings", "{nick} ändert sein Passwort.", false);
            $msg['success'] = 'Das Passwort wurde geändert!';
        }

        return $this->render('game/userconfig/password.html.twig', [
            'form' => $form,
            'config' => $config,
            'msg' => $msg ?? null
        ]);
    }

    #[Route('/game/config/logins', name: 'game.config.logins')]
    public function logins(
        UserSessionRepository      $userSessionRepository,
        UserLoginFailureRepository $userLoginFailureRepository,
        NetworkNameService         $networkNameService,
        UserSessionLogRepository   $userSessionLogRepository
    ): Response
    {

        $activeSessions = $userSessionRepository->getActiveUserSessions($this->getUser()->getData());
        $sessionLogs = $userSessionLogRepository->getSessionLogs(UserSessionSearch::create()->userId($this->getUser()->getId()), 10);
        $failures = $userLoginFailureRepository->getUserLoginFailures($this->getUser()->getId(), 10);

        return $this->render('game/userconfig/logins.html.twig', [
            'activeSessions' => $activeSessions,
            'sessionLogs' => $sessionLogs,
            'networkNameService' => $networkNameService,
            'failures' => $failures,
        ]);
    }

    #[Route('/game/config/banner', name: 'game.config.banner')]
    public function banner(UserBannerService $userBannerService): Response
    {
        $name = $userBannerService->getUserBannerPath($this->getUser()->getId());

        return $this->render('game/userconfig/banner.html.twig', [
            'banner' => file_exists($name) ? $userBannerService->getUserBannerPath($this->getUser()->getId(),true) : false,
        ]);
    }

    #[Route('/game/config/misc', name: 'game.config.misc')]
    public function misc(Request              $request,
                         UserHolidayService   $userHolidayService,
                         UserService          $userService,
                         ConfigurationService $config): Response
    {
        $user = $this->getUser()->getData();
        $form = $this->container->get('form.factory')->createNamed('hmode_form',FormType::class,$user)
            ->add('deactivate', SubmitType::class, [
                'label' => 'Urlaubsmodus deaktivieren',
                'attr' => ['style' => 'color:#0f0']
            ])
            ->add('activate', SubmitType::class, [
                'label' => 'Urlaubsmodus aktivieren',
                'attr' => ['onclick' => "return confirm('Soll der Urlaubsmodus wirklich aktiviert werden?')"]
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('activate')->isClicked()) {
                if ($userHolidayService->activateHolidayMode($user)) {
                    $msg['success'] = BBCodeUtils::toHTML("Du bist nun im Urlaubsmodus bis mind. [b]" . StringUtils::formatDate(time() + $config->getInt('hmode_days') * 24 * 3600) . "[/b].");
                    $userService->addToUserLog($user, "settings", "{nick} ist nun im Urlaub.", true);
                } else {
                    $msg['error'] = "Es sind noch Flotten unterwegs!";
                }
            }

            if ($form->get('deactivate')->isClicked()) {
                if (!$user->getDeleted() && $userHolidayService->deactivateHolidayMode($user)) {
                    $msg['success'] = "Urlaubsmodus aufgehoben! Denke daran, auf allen deinen Planeten die Produktion zu überprüfen!";
                    $userService->addToUserLog($user, "settings", "{nick} ist nun aus dem Urlaub zurück.", true);
                    $showButton = true;
                } else {
                    $msg['error'] = "Urlaubsmodus kann nicht aufgehoben werden!";
                }
            }
        }

        return $this->render('game/userconfig/misc.html.twig', [
            'form' => $form,
            'msg' => $msg ?? null,
            'showButton' => $showButton ?? false
        ]);
    }

    #[Route('/game/config/warnings', name: 'game.config.warnings')]
    public function warnings(): Response
    {
        return $this->render('game/userconfig/warnings.html.twig');
    }
}