<?php

namespace EtoA\Controller\Game;

use Doctrine\ORM\EntityManagerInterface;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Form\Type\Core\AvatarUploadType;
use EtoA\Form\Type\Core\DesignType;
use EtoA\Form\Type\Core\MultiViewType;
use EtoA\Form\Validation\SamePasswordConstraint;
use EtoA\Form\Validation\ValidUserConstraint;
use EtoA\Security\Player\CurrentPlayer;
use EtoA\Support\FileUtils;
use EtoA\User\User;
use EtoA\User\UserMulti;
use EtoA\User\UserMultiRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserSitting;
use EtoA\User\UserSittingRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EtoA\Admin\AllianceBoardAvatar;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Support\StringUtils;
use EtoA\User\ProfileImage;
use EtoA\User\UserRepository;
use EtoA\Form\Type\Core\ProfileUploadType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use EtoA\Tutorial\TutorialManager;
use EtoA\Ship\ShipDataRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Symfony\Component\Clock\now;

class UserConfigController extends AbstractGameController
{
    public function __construct(
        private readonly UserRepository     $userRepository,
        private readonly StringUtils     $stringUtils,
        private readonly MailSenderService     $mailSenderService,
        private readonly FileUtils     $fileUtils,
        private readonly ShipDataRepository     $shipDataRepository,
        private readonly UserPropertiesRepository     $userPropertiesRepository,
    ){}

    #[Route('/game/config/general', name: 'game.config.general')]
    public function general(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser()->getData();
        $msg['error'] = '';
        $msg['success'] = [];

        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class)
            ->add('profileText', TextareaType::class,['required'=>false])
            ->add('signature', TextareaType::class,['required'=>false])
            ->add('profileImage', ProfileUploadType::class)
            ->add('profileImgDel', CheckboxType::class, ['mapped' => false,'required'=>false])
            ->add('avatarDel', CheckboxType::class, ['mapped' => false,'required'=>false])
            ->add('profileBoardUrl', TextType::class,['required'=>false])
            ->add('avatar', AvatarUploadType::class)
            ->add('save', SubmitType::class, ['label' => 'Übernehmen'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->stringUtils->checkEmail($form->getData()->getEmail())) {
                // Avatar
                if ($form->get('avatarDel')->getData()) {
                    $user->setAvatar("");
                } elseif ($form->get('avatar')->getData()) {
                    if($file = $this->fileUtils->uploadImage(
                        $form->get('avatar')->getData(),
                        $this->getParameter('kernel.project_dir').AllianceBoardAvatar::IMAGE_PATH,
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
                    if($file = $this->fileUtils->uploadImage(
                        $form->get('profileImage')->getData(),
                        $this->getParameter('kernel.project_dir').ProfileImage::IMAGE_PATH,
                        [ProfileImage::IMAGE_WIDTH, ProfileImage::IMAGE_HEIGHT],
                        $msg['error']
                    )) {
                        $user->setProfileImage($file->getFilename());
                    }
                }

                //TODO: use this to check if mail has changed after entity is fully working
                /*
                $uow = $entityManager->getUnitOfWork();
                $uow->computeChangeSets();
                $changeSet = $uow->getEntityChangeSet($user);
                */

                if ($user->getEmail() !== $form->getData()->getEmail()) {
                    $subject = "Änderung deiner E-Mail-Adresse";
                    $text = "Die E-Mail-Adresse deines Accounts " . $user->getNick() . " wurde von " . $user->getEmail() . " auf " . $form->getData()['email'] . " geändert!";
                    $this->mailSenderService->send($subject, $text, $user->getEmail());
                    if ($user->getEmailFix() !== $user->getEmail()) {
                        $this->mailSenderService->send($subject, $text, $user->getEmailFix());
                    }
                    $user->setEmail($form->getData()['email']);
                }

                $this->userRepository->save($user);
                $msg['success'][] = "Benutzer-Daten wurden geändert!";
            } else
                $msg['error'] = 'Die E-Mail-Adresse ist nicht korrekt!';
        }

        return $this->render('game/userconfig/general.html.twig', [
            'msg' => $msg,
            'form' => $form,
            'imgMax' => StringUtils::formatBytes(ProfileImage::IMAGE_MAX_SIZE),
            'avatarMax' => StringUtils::formatBytes(AllianceBoardAvatar::AVATAR_MAX_SIZE),
            'profileImg' => $user->getProfileImage() ? ProfileImage::IMAGE_PATH . $user->getProfileImage() : null,
            'allianceAvatar' => $user->getAvatar() && $user->getAvatar() != AllianceBoardAvatar::DEFAULT_IMAGE ? AllianceBoardAvatar::IMAGE_PATH . $user->getAvatar():null,
        ]);
    }

    #[Route('/game/config/game', name: 'game.config.game')]
    public function game(Request $request): Response
    {
        $properties = $this->userPropertiesRepository->getOrCreateProperties($this->getUser()->getId());

        $form = $this->createFormBuilder($properties)
            ->add('spyShipCount', IntegerType::class, [
                'attr' => ['maxlength'=>5, 'size'=>5]
            ])
            ->add('spyShipId', ChoiceType::class,[
                'placeholder' =>false,
                'choices' => array_merge(['(keines)'=>0],array_flip($this->shipDataRepository->getShipNamesWithAction('spy'))),
                'required'=>false
            ]
            )
            ->add('analyzeShipCount', IntegerType::class, [
                'attr' => ['maxlength'=>5, 'size'=>5]
            ])
            ->add('analyzeShipId', ChoiceType::class,[
                'placeholder' =>false,
                'choices' => array_merge(['(keines)'=>0],array_flip($this->shipDataRepository->getShipNamesWithAction('analyze'))),
                'required'=>false
            ])
            ->add('exploreShipCount', IntegerType::class, [
                'attr' => ['maxlength'=>5, 'size'=>5]
            ])
            ->add('exploreShipId', ChoiceType::class,[
                'placeholder' =>false,
                'choices' => array_merge(['(keines)'=>0],array_flip( $this->shipDataRepository->getShipNamesWithAction('explore'))),
                'required'=>false
            ])
            ->add('startUpChat', ChoiceType::class,[
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('showCellreports', ChoiceType::class,[
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('showTut', ButtonType::class, ['label'=>'Anzeigen', 'attr'=>['onClick'=>'showTutorialText(1,0']])
            ->add('chatColor', ColorType::class,['attr'=>
                [
                    'size'=>6,
                    'onkeyup' => "addFontColor(this.id,'chatPreview')",
                    'onchange' => "addFontColor(this.id,'chatPreview')",
                ]
            ])
            ->add('enableKeybinds', ChoiceType::class,[
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
            $properties->chatColor = substr($properties->chatColor, 1);

            if ($properties->chatColor === '000' || $properties->chatColor === '000000') {
                $msg['success'] = 'Chatfarbe schwarz auf schwarz ist eine Weile ja ganz lustig, aber in ein paar Minuten bitte zurückändern';
            } else {
                $msg['success'] = 'Benutzer-Daten wurden geändert!';
            }

            //TODO: use entity manager for it
            $this->userPropertiesRepository->storeProperties($this->getUser()->getId(),$properties);
        }

        return $this->render('game/userconfig/game.html.twig', [
            'form' => $form,
            'msg' => $msg??null,
            'chatColor' => $properties->chatColor
        ]);
    }

    #[Route('/game/config/messages', name: 'game.config.messages')]
    public function messages(Request $request): Response
    {
        $properties = $this->userPropertiesRepository->getOrCreateProperties($this->getUser()->getId());

        $form = $this->createFormBuilder($properties)
            ->add('msgSignature', TextareaType::class, [
                'attr' => ['cols'=>50, 'rows'=>4]
            ])
            ->add('msgPreview', ChoiceType::class,[
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('msgCreationPreview', ChoiceType::class,[
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('msgBlink', ChoiceType::class,[
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('msgCopy', ChoiceType::class,[
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('fleetRtnMsg', ChoiceType::class,[
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
            $this->userPropertiesRepository->storeProperties($this->getUser()->getId(),$properties);
            $msg['success'] = 'Nachrichten-Einstellungen wurden geändert!';
        }

        return $this->render('game/userconfig/messages.html.twig', [
            'form' => $form,
            'msg' => $msg??null,
        ]);
    }

    #[Route('/game/config/design', name: 'game.config.design')]
    public function design(Request $request): Response
    {
        $properties = $this->userPropertiesRepository->getOrCreateProperties($this->getUser()->getId());
        $planetWidth = [];

        for ($x = 450; $x <= 700; $x += 50) {
            $planetWidth[$x] = $x;
        }

        $form = $this->createFormBuilder($properties)
            ->add('cssStyle', DesignType::class)
            ->add('planetCircleWidth', ChoiceType::class,[
                'choices' => $planetWidth,
            ])
            ->add('itemShow', ChoiceType::class,[
                'choices' => [
                    ' Volle Ansicht ' => 'full',
                    ' Einfache Ansicht ' => 'small',
                ],
                'expanded' => true,
            ])
            ->add('imageFilter', ChoiceType::class,[
                'choices' => [
                    ' An ' => 1,
                    ' Aus ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('showAdds', ChoiceType::class,[
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('smallResBox', ChoiceType::class,[
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
            $this->userPropertiesRepository->storeProperties($this->getUser()->getId(),$properties);
            $msg['success'] = 'Design-Daten wurden geändert!';
        }

        return $this->render('game/userconfig/design.html.twig', [
            'form' => $form,
            'msg' => $msg??null,
        ]);
    }

    #[Route('/game/config/sitting', name: 'game.config.sitting')]
    public function sitting(Request $request, UserMultiRepository $userMultiRepository, UserSittingRepository $userSittingRepository): Response
    {
        $multiEntries = $userMultiRepository->getUserEntries($this->getUser()->getId(), true);

        if($multiEntries) {
            $form = $this->createFormBuilder(['userMultis' => $multiEntries])
                ->add('save', SubmitType::class, ['label' => 'Übernehmen'])
                ->add('userMultis', CollectionType::class, array(
                    'entry_type' => MultiViewType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => ['label' => false],
                ))
                ->getForm();
        }
        else {
            $form = $this->createFormBuilder()
                ->add('save', SubmitType::class, ['label' => 'Übernehmen'])
                ->getForm();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // User löschen
            foreach ($form->get('userMultis') as $userMulti) {
                if($userMulti->get('delMulti')->getData()) {
                    $entry = $userMultiRepository->getUserEntry($this->getUser()->getId(), $userMulti->getData()->multiUserId);
                    if ($entry !== null) {
                        if ($entry->reason !== '0' && $entry->multiUserId !== 0) {
                            $userMultiRepository->deleteEntry($entry->id);
                        } else {
                            $userMultiRepository->deactivateEntry($entry->id);
                            // Speichert jeden gelöschten multi (soll vor missbrauch schützen -> mutli erstellen -> löschen -> erstellen -> löschen etc.)
                            $this->userRepository->increaseMultiDeletes($this->getUser()->getId());
                        }
                        $msg['success'] = "Eintrag gelöscht!";

                        //removing entity after passing it to the form collection will show outdated values
                        //TODO: find a way to update form collection
                        $this->redirectToRoute('game.config.sitting');
                    }
                }
            }
        }

        if ($request->get('remove_sitting') && intval($request->get('remove_sitting')) > 0) {
            $success = $userSittingRepository->deleteFutureUserEntry((int) $request->get('remove_sitting'), $this->getUser()->getId());
            if ($success)
                $msg['success'] = "Sitting entfernt!";
        }
        if ($request->get('cancel_sitting') && intval($request->get('cancel_sitting')) > 0) {
            $success = $userSittingRepository->cancelUserEntry((int) $request->get('cancel_sitting'), $this->getUser()->getId());
            if ($success)
                $msg['success'] = "Sitting entfernt!";
        }

        return $this->render('game/userconfig/sitting.html.twig', [
            'form' => $form,
            'msg' => $msg??null,
            'sittingEntries' => $userSittingRepository->getWhereUser($this->getUser()->getId())
        ]);
    }

    #[Route('/game/config/sitting/new', name: 'game.config.sitting.new')]
    public function sittingAdd(Request $request, UserSittingRepository $userSittingRepository, ConfigurationService $config): Response
    {
        $sittingDays = max(0, $this->getUser()->getData()->getSittingDays() - $userSittingRepository->getUsedSittingTime($this->getUser()->getId()));
        $sittingLeft = [];

        if(!$sittingDays) {
            $msg['error'] = 'Alle Sitting-Tage sind aufgebraucht!';
            return $this->render('game/userconfig/sitting_add.html.twig', [
                'msg' => $msg
            ]);
        }

        for ($x = 1; $sittingDays >= $x; $x++) {
            $sittingLeft[$x] = $x;
        }

        $userSitting = new UserSitting([
            'id'=> 0,
            'user_id' => 0,
            'user_nick' => '',
            'sitter_id' => 0,
            'sitter_nick' => '',
            'password' => '',
            'date_from' => 0,
            'date_to' => 0,
        ]);

        $form = $this->createFormBuilder($userSitting)
            ->add('save', SubmitType::class, ['label' => 'Speichern'])
            ->add('sitterNick', TextType::class,[
                'attr'=>[
                    'autocomplete' => 'off',
                    'maxlength'=>"20",
                    'size'=>"20",
                ],
                'constraints' => [
                    new ValidUserConstraint(),
                    new NotBlank(['message'=>'Kein Name angegeben!']),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Passwörter sind nicht gleich oder zu kurz (mind. %min% Zeichen)',
                'invalid_message_parameters' => ['%min%' => $config->getInt('password_minlength')],
                'options' => [
                    'attr'=>[
                        'autocomplete' => 'off',
                        'maxlength'=>"20",
                        'size'=>"20",
                        'minlength'=>$config->getInt('password_minlength'),
                    ]
                ],
                'required' => true,
                'constraints' => [
                    new Length(['min' => $config->getInt('password_minlength')])
                ],
                'first_options' => [
                    'hash_property_path' => 'password',
                    'constraints' => [
                        new SamePasswordConstraint()
                    ],
                ],
                'mapped' => false,
            ])
            ->add('dateFrom', DateTimeType::class,[
                'widget' => 'single_text',
                'attr' => ['min'=>now()->format("Y-m-d H:i")],
                'data' => now(),
                'mapped' => false
            ])
            ->add('dateToDays', ChoiceType::class,[
                'choices' => $sittingLeft,
                'mapped' => false
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $tm_from = $form->get('dateFrom')->getData()->getTimestamp();
            $tm_to = $tm_from + $form->get('dateToDays')->getData() * 86400;

            if ($tm_from > time() - 600  && $tm_from < $tm_to && $form->get('dateToDays')->getData() <= $sittingDays) {
                if (!$userSittingRepository->hasSittingEntryForTimeSpan($this->getUser()->getId(), $tm_from, $tm_to)) {
                    $sitterUser = $this->userRepository->getUserByNick($userSitting->sitterNick);
                    $userSittingRepository->addEntry($this->getUser()->getId(), $sitterUser->getId(), $userSitting->getPassword(), $tm_from, $tm_to);
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
            'msg' => $msg??null
        ]);
    }

    #[Route('/game/config/dual', name: 'game.config.dual')]
    public function dual(Request $request): Response
    {

        $user = $this->getUser()->getData();
        $formInterface = $this->createFormBuilder($user);
        $formInterface->add('dualName', TextType::class,[
            'required'=>false,
            'attr' => ['maxlength'=>"255", 'size'=>"30"],
            'constraints' => [
                new NotBlank([
                    'message' => 'Es ist kein Name angegeben!',
                ])
            ],
        ]);

        $formInterface->add('dualEmail', EMailType::class,[
            'required'=>false,
            'attr' => ['maxlength'=>"255", 'size'=>"30"],
            'constraints' => [
                new Email([
                    'message' => 'Die E-Mail-Adresse ist nicht korrekt!',
                ])
            ],
        ]);

        $form = $formInterface->add('save', SubmitType::class, ['label' => 'Übernehmen'])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->save($this->getUser()->getdata());
        }

        return $this->render('game/userconfig/dual.html.twig', [
            'form' => $form,
            'msg' => $msg??null,
        ]);
    }

    #[Route('/game/config/password', name: 'game.config.password')]
    public function password(Request $request): Response
    {

    }

    #[Route('/game/config/logins', name: 'game.config.logins')]
    public function logins(Request $request): Response
    {

    }

    #[Route('/game/config/banner', name: 'game.config.banner')]
    public function banner(Request $request): Response
    {

    }

    #[Route('/game/config/misc', name: 'game.config.misc')]
    public function misc(Request $request): Response
    {

    }

    #[Route('/game/config/warnings', name: 'game.config.warnings')]
    public function warnings(Request $request): Response
    {

    }
}