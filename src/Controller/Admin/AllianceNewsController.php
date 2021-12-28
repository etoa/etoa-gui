<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Alliance\AllianceNewsRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Form\Type\Admin\AllianceNewsCleanupType;
use EtoA\Form\Type\Admin\AllianceNewsDefaultBanType;
use EtoA\Form\Type\Admin\AllianceNewsEditType;
use EtoA\User\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllianceNewsController extends AbstractAdminController
{
    public function __construct(
        private AllianceNewsRepository $allianceNewsRepository,
        private UserRepository $userRepository,
        private ConfigurationService $config
    ) {
    }

    #[Route('/admin/alliances/news', name: 'admin.alliances.news')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function news(Request $request): Response
    {
        $cleanupForm = $this->createForm(AllianceNewsCleanupType::class);
        $cleanupForm->handleRequest($request);
        if ($cleanupForm->isSubmitted() && $cleanupForm->isValid()) {
            $deleted = $this->allianceNewsRepository->deleteOlderThan(time() - $cleanupForm->getData()['timespan']);
            $this->addFlash('success', $deleted . " Beiträge wurden gelöscht!");
        }

        $defaultBanForm = $this->createForm(AllianceNewsDefaultBanType::class);
        $defaultBanForm->handleRequest($request);
        if ($defaultBanForm->isSubmitted() && $defaultBanForm->isValid()) {
            $this->config->set('townhall_ban', $defaultBanForm->getData()['timespan'], $defaultBanForm->getData()['reason']);
            $this->addFlash('success', "Einstellungen gespeichert!");
        }

        return $this->render('admin/alliance/news.html.twig', [
            'cleanupForm' => $cleanupForm->createView(),
            'defaultBanForm' => $defaultBanForm->createView(),
            'newsEntries' => $this->allianceNewsRepository->getNewsEntries(null),
        ]);
    }

    #[Route('/admin/alliances/news/{id}/edit', name: 'admin.alliances.news.edit')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function edit(int $id, Request $request): Response
    {
        $news = $this->allianceNewsRepository->getEntry($id);

        $form = $this->createForm(AllianceNewsEditType::class, $news);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->allianceNewsRepository->update($news);

            $this->addFlash('success', "Beitrag wurde aktualisiert!");

            return $this->redirectToRoute('admin.alliances.news');
        }

        return $this->render('admin/alliance/news-edit.html.twig', [
            'form' => $form->createView(),
            'news' => $news,
        ]);
    }

    #[Route('/admin/alliances/news/{id}/delete', name: 'admin.alliances.news.delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function delete(int $id): Response
    {
        $this->allianceNewsRepository->deleteEntry($id);
        $this->addFlash('success', "Beitrag wurde gelöscht!");

        return $this->redirectToRoute('admin.alliances.news');
    }

    #[Route('/admin/alliances/news/{id}/ban', name: 'admin.alliances.news.ban', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function ban(int $id): Response
    {
        $t1 = time();
        $t2 = $t1 + $this->config->getInt('townhall_ban');
        $news = $this->allianceNewsRepository->getEntry($id);
        $this->userRepository->blockUser($news->authorUserId, $t1, $t2, $this->config->param1('townhall_ban'), $this->getUser()->getId());
        $this->addFlash('success', "Der Benutzer wurde gesperrt!");

        return $this->redirectToRoute('admin.alliances.news');
    }
}
