<?php

namespace EtoA\Controller\External;

use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Controller\AbstractLegacyShowController;
use EtoA\Core\AppName;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\HostCache\NetworkNameService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Text\TextRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractLegacyShowController
{
    protected ?string $pageTitle = 'Kontakt';

    #[Route('/contact-support', name: 'external.contact')]
    public function index(
        TextRepository       $textRepo,
        AdminUserRepository  $adminUserRepo,
        ConfigurationService $config,
    ): Response
    {
        $admins = array_filter($adminUserRepo->findAll(), fn(AdminUser $admin) => $admin->isContact);

        return $this->handle(function () use (
            $textRepo,
            $adminUserRepo,
            $config,
            $admins,
        ) {
            ob_start();

            $contactText = $textRepo->find('contact_message');
            if ($contactText->isEnabled()) {
                iBoxStart();
                echo BBCodeUtils::toHTML($contactText->content);
                iBoxEnd();
            }

            if (count($admins) > 0) {
                tableStart('Kontaktpersonen für die ' . $config->get('roundname'));
                echo '<tr>
                <th>Name</th>
                <th>Mail</th>
                <th>Kontaktformular</th>
                <th>Foren-Profil</th>
            </tr>';
                foreach ($admins as $admin) {
                    $showMailAddress = preg_match('/' . AdminUser::CONTACT_REQUIRED_EMAIL_SUFFIX . '/i', $admin->email);

                    echo '<tr><td>' . $admin->nick . '</td>';
                    if ($showMailAddress) {
                        echo '<td><a href="mailto:' . $admin->email . '">' . $admin->email . '</a></td>';
                    } else {
                        echo '<td>(nicht öffentlich)</td>';
                    }
                    echo '<td><a href="' . $this->generateUrl('external.contact.message', ['adminId' => $admin->id]) . '">Mail senden</a></td>';
                    if ($admin->boardUrl != '') {
                        echo '<td><a href="' . $admin->boardUrl . '" target="_blank">Profil</a></td>';
                    } else {
                        echo '<td>-</td>';
                    }
                    echo '</tr>';
                }
                tableEnd();
            } else {
                info_msg('Keine Kontaktpersonen vorhanden!');
            }

            return $this->render('external/contact.html.twig', [
                'contactContent' => ob_get_clean(),
            ]);
        });
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
        return $this->handle(function () use (
            $adminUserRepo,
            $config,
            $mailSenderService,
            $networkNameService,
            $adminId,
            $request,
        ) {
            ob_start();

            $admin = $this->getAdmin($adminUserRepo, $adminId);
            if ($admin === null) {
                $this->addFlash('error', "Kontakt nicht vorhanden!");
                return $this->redirectToRoute('external.contact');
            }

            $sender = '';
            $mail_subject = '';
            $mail_text = '';

            if ($request->request->has('submit')) {
                $mail_subject = $request->get('mail_subject');
                $mail_text = $request->get('mail_text');
                $sender = $request->request->get('mail_sender');

                if (filled($mail_subject) && filled($mail_text) && filled($sender)) {
                    $subject = "Kontakt-Anfrage: " . $mail_subject;
                    $recipient = $admin->email;

                    // Text
                    $text = "Kontakt-Anfrage " . AppName::NAME . " " . $config->get('roundname') . "\n----------------------\n\n";
                    $text .= "E-Mail: " . $sender . "\n";
                    $text .= "IP/Host: " . $request->getClientIp() . " (" . $networkNameService->getHost($request->getClientIp()) . ")\n\n";
                    $text .= $mail_text;

                    // Send mail
                    try {
                        $mailSenderService->send($subject, $text, $recipient, $sender);
                        $this->addFlash('success', 'Vielen Dank! Deine Nachricht wurde gesendet!');

                        return $this->redirectToRoute('external.contact');
                    } catch (Exception $ex) {
                        $this->addFlash('error', $ex->getMessage());
                    }
                } else {
                    $this->addFlash('error', "Titel oder Text fehlt!");
                }
            }

            echo '<form action="' . $this->generateUrl('external.contact.message', ['adminId' => $admin->id]) . '" method="post"><div>';
            tableStart('Nachricht an ' . $admin->nick . ' senden');
            echo '<tr><th>Absender E-Mail:</th><td><input type="email" name="mail_sender" value="' . $sender . '" size="50" autofocus required />';
            echo '</td></tr>';
            echo '<tr><th>Titel:</th><td><input type="text" name="mail_subject" value="' . $mail_subject . '" size="50" required /></td></tr>';
            echo '<tr><th>Text:</th><td><textarea name="mail_text" rows="6" cols="80" required>' . $mail_text . '</textarea></td></tr>';
            tableEnd();
            echo '<input type="submit" name="submit" value="Senden" /> &nbsp;';
            echo '<input type="button" onclick="document.location=\'' . $this->generateUrl('external.contact') . '\'" value="Zurück" /></div></form>';

            return $this->render('external/contact.html.twig', [
                'contactContent' => ob_get_clean(),
            ]);
        });
    }

    private function getAdmin(AdminUserRepository $adminUserRepo, int $adminId): ?AdminUser
    {
        $admins = array_filter($adminUserRepo->findAll(), fn(AdminUser $admin) => $admin->isContact);
        $admins = array_filter($admins, fn(AdminUser $admin) => $admin->id == $adminId);
        return array_values($admins)[0] ?? null;
    }
}