<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\MessageSearchType;
use EtoA\Message\MessageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractAdminController
{
    public function __construct(
        private MessageRepository $messageRepository
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
}
