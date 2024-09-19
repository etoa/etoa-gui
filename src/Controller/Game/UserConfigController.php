<?php

namespace EtoA\Controller\Game;

use EtoA\Controller\Game\AbstractGameController;
use EtoA\Form\Type\Core\AvatarUploadType;
use EtoA\Support\FileUtils;
use EtoA\User\UserPropertiesRepository;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
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

class UserConfigController extends AbstractGameController
{
    public function __construct(
        private readonly UserRepository     $userRepository,
        private readonly StringUtils     $stringUtils,
        private readonly MailSenderService     $mailSenderService,
        private readonly FileUtils     $fileUtils,
        private readonly TutorialManager     $tutorialManager,
        private readonly ShipDataRepository     $shipDataRepository,
        private readonly UserPropertiesRepository     $userPropertiesRepository,
    ){}

    #[Route('/game/config/general', name: 'game.config.general')]
    public function general(Request $request): Response
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

                if ($user->getEmail() !== $form->getData()->getEmail()) {
                    $subject = "Änderung deiner E-Mail-Adresse";
                    $text = "Die E-Mail-Adresse deines Accounts " . $user->getNick() . " wurde von " . $user->getEmail() . " auf " . $form->getData()['email'] . " geändert!";
                    $this->mailSenderService->send($subject, $text, $user->getEmail());
                    if ($user->getEmailFix() !== $user->getEmail()) {
                        $this->mailSenderService->send($subject, $text, $user->getEmailFix());
                    }
                    $user->setEmail($form->getData()['email']);
                }

                $user->setProfileText(addslashes($form->getData()->getProfileText()));
                $user->setSignature(addslashes($form->getData()->getSignature()));
                $user->setProfileBoardUrl($form->getData()->getProfileBoardUrl());
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
        $msg['success'] = [];
        $msg['error'] = '';

        $form = $this->createFormBuilder($properties)
            ->add('spyShipCount', IntegerType::class, [
                'attr' => ['maxlength'=>5, 'size'=>5]
            ])
            ->add('spyShipId', ChoiceType::class,[
                'choices' => [
                    '(keines)' => '0',
                    'choices' => array_flip($this->shipDataRepository->getShipNamesWithAction('spy')),
                ]
            ])
            ->add('analyzeShipCount', IntegerType::class, [
                'attr' => ['maxlength'=>5, 'size'=>5]
            ])
            ->add('analyzeShipId', ChoiceType::class,[
                'choices' => [
                    '(keines)' => '0',
                    'choices' => array_flip($this->shipDataRepository->getShipNamesWithAction('analyze')),
                ]
            ])
            ->add('exploreShipCount', IntegerType::class, [
                'attr' => ['maxlength'=>5, 'size'=>5]
            ])
            ->add('exploreShipId', ChoiceType::class,[
                'choices' => [
                    '(keines)' => '0',
                    'choices' => array_flip($this->shipDataRepository->getShipNamesWithAction('explore')),
                ]
            ])
            ->add('startUpChat', ChoiceType::class,[
                'choices' => [
                    ' Aktiviert ' => '1',
                    ' Deaktiviert ' => '0',
                ],
                'expanded' => true,
            ])
            ->add('showCellreports', ChoiceType::class,[
                'choices' => [
                    ' Aktiviert ' => '1',
                    ' Deaktiviert ' => '0',
                ],
                'expanded' => true,
            ])
            ->add('showTut', ButtonType::class)
            ->add('chatColor', ColorType::class,['attr'=>
                [
                    'size'=>6,
                    'onkeyup' => "addFontColor(this.id,'chatPreview')",
                    'onchange' => "addFontColor(this.id,'chatPreview')",
                ]
            ])
            ->add('enableKeybinds', ChoiceType::class,[
                'choices' => [
                    ' Aktiviert ' => '1',
                    ' Deaktiviert ' => '0',
                ],
                'expanded' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'Übernehmen'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getData()->getChatColor() == '000' || $form->getData()->getChatColor() == '000000') {
                success_msg('Chatfarbe schwarz auf schwarz ist eine Weile ja ganz lustig, aber in ein paar Minuten bitte zur&uuml;ck&auml;ndern ;)');
            } else {
                success_msg('Benutzer-Daten wurden ge&auml;ndert!');
            }
        }
        return $this->render('game/userconfig/game.html.twig', [
            'form' => $form,
            'msg' => $msg,
            'chatColor' => $properties->chatColor
        ]);
    }

    #[Route('/game/config/messages', name: 'game.config.messages')]
    public function messages(Request $request): Response
    {

    }

    #[Route('/game/config/design', name: 'game.config.design')]
    public function design(Request $request): Response
    {

    }

    #[Route('/game/config/sitting', name: 'game.config.sitting')]
    public function sitting(Request $request): Response
    {

    }

    #[Route('/game/config/dual', name: 'game.config.dual')]
    public function dual(Request $request): Response
    {

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