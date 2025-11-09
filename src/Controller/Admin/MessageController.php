<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Entity\MessageData;
use EtoA\Form\Type\Admin\MessageSearchType;
use EtoA\Form\Type\Admin\MessageSendType;
use EtoA\Form\Type\Admin\ReportSearchType;
use EtoA\Message\AdminMessageRequest;
use EtoA\Message\MessageDataRepository;
use EtoA\Message\MessageRepository;
use EtoA\Message\ReportRepository;
use EtoA\Support\Mail\MailSenderService;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MessageController extends AbstractAdminController
{
    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly UserRepository    $userRepository,
        private readonly MailSenderService $mailSenderService,
        private readonly ReportRepository  $reportRepository
    )
    {
    }

    #[Route('/admin/messages/', name: 'admin.messages')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function search(Request $request): Response
    {
        return $this->render('admin/message/search.html.twig', [
            'form' => $this->createForm(MessageSearchType::class, $request->query->all()),
            'total' => $this->messageRepository->count([]),
        ]);
    }

    #[Route('/admin/messages/reports', name: 'admin.messages.reports')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function reports(Request $request): Response
    {
        return $this->render('admin/message/reports.html.twig', [
            'form' => $this->createForm(ReportSearchType::class, $request->query->all()),
            'total' => $this->reportRepository->count([]),
        ]);
    }

    #[Route('/admin/messages/send', name: 'admin.messages.send')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function sendMessage(Request $request): Response
    {
        $messageRequest = AdminMessageRequest::fromRequest($request);
        $form = $this->createForm(MessageSendType::class, $messageRequest, ['admin_player' => $this->getUser()->getData()?->getPlayer()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $msgCnt = 0;
            if ($messageRequest->sendAsInGameMessage()) {
                if ($messageRequest->user === null) {
                    $users = $this->userRepository->searchUserNicknames();
                } else {
                    $users = [$messageRequest->user];
                }

                foreach ($users as $user) {
                    $messageData = new MessageData();
                    $messageData->setText($messageRequest->text);
                    $messageData->setSubject($messageRequest->subject);

                    $this->messageRepository->sendFromUserToUser(
                        $messageRequest->from,
                        $user,
                        $messageData
                    );
                    $msgCnt++;
                }
            }

            if ($msgCnt > 0) {
                $this->addFlash('success', "$msgCnt InGame-Nachrichten wurden versendet!");
            }

            $mailCnt = 0;
            if ($messageRequest->sendAsEmail()) {
                if ($messageRequest->user === null) {
                    $recipients = $this->userRepository->getEmailAddressesWithNickname();
                } else {
                    $recipient = $messageRequest->user;
                    $recipients = [$recipient->getEmail() => $recipient->getNick()];
                }

                if ($messageRequest->from) {
                    $replyUser = $this->getUser()->getData()->getPlayer();
                    $replyTo = [$replyUser->getEmail() => $replyUser->getNick()];
                } else {
                    $replyTo = null;
                }

                $this->mailSenderService->send(
                    $messageRequest->subject,
                    $messageRequest->text,
                    $recipients,
                    $replyTo
                );
                $mailCnt++;
            }

            if ($mailCnt > 0) {
                $this->addFlash('success', "$mailCnt Mails wurden versendet!");
            }
        }

        return $this->render('admin/message/send.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
