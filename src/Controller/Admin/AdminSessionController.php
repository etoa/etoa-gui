<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminSessionManager;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Form\Type\Admin\AdminSessionLogType;
use EtoA\Form\Type\Admin\DeleteAdminSessionLogType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminSessionController extends AbstractAdminController
{
    public function __construct(
        private AdminSessionManager $sessionManager,
        private LogRepository $logRepository,
        private ConfigurationService $config,
        private AdminSessionRepository $sessionRepository
    ) {
    }

    /**
     * @Route("/admin/admin-sessions/", name="admin.admin-sessions")
     */
    public function list(): Response
    {
        return $this->render('admin/admin-session/list.html.twig', [
            'sessionTimeout' => $this->config->getInt('admin_timeout'),
            'time' => time(),
            'sessions' => $this->sessionRepository->findAll(),
            'sessionLogForm' => $this->createForm(AdminSessionLogType::class)->createView(),
            'deleteSessionForm' => $this->createForm(DeleteAdminSessionLogType::class)->createView(),
            'sessionLogCount' => $this->sessionRepository->countSessionLog(),
        ]);
    }

    /**
     * @Route("/admin/admin-sessions/{id}/kick", name="admin.admin-sessions.kick", methods={"POST"})
     */
    public function kick(Request $request, string $id): RedirectResponse
    {
        if ($id === $request->getSession()->getId()) {
            $this->addFlash('error', "Du kannst nicht dich selbst kicken!");
        } else {
            $this->sessionManager->kick($id);
            $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $this->getUser()->getUsername() . " löscht die Session des Administrators mit der ID " . $id);
        }

        return $this->redirectToRoute('admin.admin-sessions');
    }

    /**
     * @Route("/admin/admin-sessions/delete", name="admin.admin-sessions.delete", methods={"POST"})
     */
    public function deleteEntries(Request $request): RedirectResponse
    {
        $form = $this->createForm(DeleteAdminSessionLogType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $nr = $this->sessionManager->cleanupLogs((int) $form->getData()['timespan']);

            $this->addFlash('success', $nr . " Einträge wurden gelöscht!");
        }

        return $this->redirectToRoute('admin.admin-sessions');
    }
}
