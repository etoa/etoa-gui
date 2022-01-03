<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\MessageSearchType;
use EtoA\Form\Type\Admin\MessageSendType;
use EtoA\Form\Type\Admin\ReportSearchType;
use EtoA\Message\AdminMessageRequest;
use EtoA\Message\MessageRepository;
use EtoA\Message\ReportRepository;
use EtoA\Support\Mail\MailSenderService;
use EtoA\User\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractAdminController
{
    public function __construct(
        private MessageRepository $messageRepository,
        private UserRepository $userRepository,
        private MailSenderService $mailSenderService,
        private ReportRepository $reportRepository
    ) {
    }

    #[Route('/admin/messages/', name: 'admin.messages')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function search(Request $request): Response
    {
        return $this->render('admin/message/search.html.twig', [
            'form' => $this->createForm(MessageSearchType::class, $request->query->all())->createView(),
            'total' => $this->messageRepository->count(),
        ]);
    }

    #[Route('/admin/messages/reports', name: 'admin.messages.reports')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function reports(Request $request): Response
    {
        return $this->render('admin/message/reports.html.twig', [
            'form' => $this->createForm(ReportSearchType::class, $request->query->all())->createView(),
            'total' => $this->reportRepository->count(),
        ]);
    }

    #[Route('/admin/messages/send', name: 'admin.messages.send')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function sendMessage(Request $request): Response
    {
        $messageRequest = AdminMessageRequest::fromRequest($request);
        $form = $this->createForm(MessageSendType::class, $messageRequest, ['admin_player_id' => $this->getUser()->getData()->playerId]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $msgCnt = 0;
            if ($messageRequest->sendAsInGameMessage()) {
                if ($messageRequest->userId === null) {
                    $userIds = array_keys($this->userRepository->searchUserNicknames());
                } else {
                    $userIds = [$messageRequest->userId];
                }

                foreach ($userIds as $userId) {
                    $this->messageRepository->sendFromUserToUser(
                        $messageRequest->fromId,
                        $userId,
                        $messageRequest->subject,
                        $messageRequest->text
                    );
                    $msgCnt++;
                }
            }

            if ($msgCnt > 0) {
                $this->addFlash('success', "$msgCnt InGame-Nachrichten wurden versendet!");
            }

            $mailCnt = 0;
            if ($messageRequest->sendAsEmail()) {
                if ($messageRequest->userId === null) {
                    $recipients = $this->userRepository->getEmailAddressesWithNickname();
                } else {
                    $recipient = $this->userRepository->getUser($messageRequest->userId);
                    $recipients = [$recipient->email => $recipient->nick];
                }

                if ($messageRequest->fromId > 0) {
                    $replyUser = $this->userRepository->getUser($this->getUser()->getData()->playerId);
                    $replyTo = [$replyUser->email => $replyUser->nick];
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
