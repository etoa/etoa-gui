<?php

namespace EtoA\Controller\External;

use EtoA\Admin\AdminUserRepository;
use EtoA\Core\AppName;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\AdminUser;
use EtoA\HostCache\NetworkNameService;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Text\TextRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactController extends AbstractController
{
    #[Route('/contact-support', name: 'external.contact')]
    public function index(
        TextRepository       $textRepo,
        AdminUserRepository  $adminUserRepo,
        ConfigurationService $config,
    ): Response
    {
        $admins = array_filter($adminUserRepo->findAll(), fn(AdminUser $admin) => $admin->isIsContact());
        $contactText = $textRepo->find('contact_message');

        return $this->render('external/contact.html.twig', [
            'contactText' => $contactText !== null && $contactText->isEnabled() ? $contactText->getContent() : null,
            'roundName' => $config->get('roundname'),
            'contacts' => array_map(fn (AdminUser $admin) => [
                'id' => $admin->getId(),
                'nick' => $admin->getNick(),
                // only addresses on the official domain are shown publicly
                'email' => preg_match('/' . AdminUser::CONTACT_REQUIRED_EMAIL_SUFFIX . '/i', $admin->getEmail())
                    ? $admin->getEmail()
                    : null,
                'boardUrl' => $admin->getBoardUrl(),
            ], array_values($admins)),
        ]);
    }

    #[Route('/contact-support/{adminId}', name: 'external.contact.message')]
    public function showMessageForm(
        AdminUserRepository  $adminUserRepo,
        ConfigurationService $config,
        MailSenderService    $mailSenderService,
        NetworkNameService   $networkNameService,
        Request              $request,
        int                  $adminId,
    ): Response
    {
        $admin = $this->getAdmin($adminUserRepo, $adminId);
        if ($admin === null) {
            $this->addFlash('error', "Kontakt nicht vorhanden!");
            return $this->redirectToRoute('external.contact');
        }

        $form = $this->createFormBuilder()
            ->add('mail_sender', EmailType::class, [
                'label' => 'Absender E-Mail:',
                'attr' => [
                    'size' => 50,
                    'autofocus' => 1,
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('mail_subject', TextType::class, [
                'label' => 'Titel:',
                'attr' => [
                    'size' => 50,
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('mail_text', TextareaType::class, [
                'label' => 'Text:',
                'attr' => [
                    'rows' => 6,
                    'cols' => 80,
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Senden',
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $sender = $form->get('mail_sender')->getData();

                $text = "Kontakt-Anfrage " . AppName::NAME . " " . $config->get('roundname') . "\n----------------------\n\n";
                $text .= "E-Mail: " . $sender . "\n";
                $text .= "IP/Host: " . $request->getClientIp() . " (" . $networkNameService->getHost($request->getClientIp()) . ")\n\n";
                $text .= $form->get('mail_text')->getData();

                try {
                    $mailSenderService->send(
                        "Kontakt-Anfrage: " . $form->get('mail_subject')->getData(),
                        $text,
                        $admin->getEmail(),
                        $sender
                    );
                    $this->addFlash('success', 'Vielen Dank! Deine Nachricht wurde gesendet!');

                    return $this->redirectToRoute('external.contact');
                } catch (Exception $ex) {
                    $this->addFlash('error', $ex->getMessage());
                }
            } else {
                $this->addFlash('error', "Titel oder Text fehlt!");
            }
        }

        return $this->render('external/contact_message.html.twig', [
            'admin' => $admin,
            'form' => $form,
        ]);
    }

    private function getAdmin(AdminUserRepository $adminUserRepo, int $adminId): ?AdminUser
    {
        $admins = array_filter($adminUserRepo->findAll(), fn(AdminUser $admin) => $admin->isIsContact());
        $admins = array_filter($admins, fn(AdminUser $admin) => $admin->getId() == $adminId);
        return array_values($admins)[0] ?? null;
    }
}