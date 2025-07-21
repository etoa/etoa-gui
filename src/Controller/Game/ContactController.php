<?php

namespace EtoA\Controller\Game;

use EtoA\Admin\AdminUserRepository;
use EtoA\Core\AppName;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\AdminUser;
use EtoA\HostCache\NetworkNameService;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Text\TextRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactController extends AbstractGameController
{

    public function __construct(
        private readonly AdminUserRepository $adminUserRepository,
        private readonly TextRepository $textRepository,
        private readonly ConfigurationService $configurationService,
        private readonly NetworkNameService $networkNameService,
        private readonly MailSenderService $mailSenderService
    )
    {}

    #[Route('/game/contact', name: 'game.contact.list')]
    public function list(): Response
    {
        $admins = $this->adminUserRepository->findBy(['isContact'=>true]);

        if($admins) {
            return $this->render('game/contact/list.html.twig', [
                'admins' => $admins,
                'text' => $this->textRepository->getEnabledTextOrDefault('contact_message')
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Keine Kontaktpersonen vorhanden!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Kontakt'
        ]);
    }

    #[Route('/game/contact/{id}', name: 'game.contact.mail')]
    public function mail(Request $request, ?AdminUser $adminUser = null): Response
    {
        if($adminUser && $adminUser->isIsContact()) {
            $form = $this->createFormBuilder()
                ->add('subject',TextType::class, [
                    'label' =>false,
                    'attr' => [
                        'size'=>50,
                    ],
                    'constraints' => new NotBlank(
                        ['message' => 'Du musst einen Betreff eingeben!']
                    ),
                ])
                ->add('message',TextareaType::class, [
                    'label' =>false,
                    'attr' => [
                        'rows'=>6,
                        'cols'=>80,
                    ],
                    'constraints' => new NotBlank(
                        ['message' => 'Du musst eine Nachricht eingeben!']
                    ),
                ])
                ->add('send', SubmitType::class, ['label' => 'Senden'])
                ->getForm()->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $mail_subject = $form->getData()['subject'];
                $mail_text = $form->getData()['message'];

                // Subject
                $subject = "Kontakt-Anfrage: " . $mail_subject;

                // Sender, receiver
                $recipient = [$adminUser->getEmail() => '<' . $adminUser->getNick() . '>'];
                $sender = [$this->getUser()->getData()->getEmail() => '<' . $this->getUser()->getUserIdentifier() . '>'];

                // Text
                $text = "Kontakt-Anfrage " . AppName::NAME . " " . $this->configurationService->get('roundname') . "\n----------------------\n\n";
                $text .= "Nick: " . $this->getUser()->getUserIdentifier() . "\n";
                $text .= "ID: " . $this->getUser()->getId() . "\n";



                $text .= "IP/Host: " . $request->server->get('REMOTE_ADDR') . " (" . $this->networkNameService->getHost($request->server->get('REMOTE_ADDR')) . ")\n\n";
                $text .= $mail_text;

                // Send mail
                $this->mailSenderService->send($subject, $text, $recipient, $sender);

                return $this->render('game/success.html.twig',[
                    'msg' => 'Vielen Dank! Deine Nachricht wurde gesendet!',
                    'path' => $this->generateUrl('game.contact.list'),
                    'headline' => 'Kontakt'
                ]);
            }

            return $this->render('game/contact/mail.html.twig', [
                'form' => $form,
                'admin' => $adminUser,
                'msg' => $msg??null,
                'text' => $this->textRepository->getEnabledTextOrDefault('contact_message')
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Kontaktperson nicht vorhanden!',
            'path' => $this->generateUrl('game.contact.list'),
            'headline' => 'Kontakt'
        ]);
    }
}