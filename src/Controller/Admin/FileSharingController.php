<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Support\StringUtils;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FileSharingController extends AbstractAdminController
{
    public function __construct(
        private readonly string $adminFileSharingDir
    )
    {
    }

    #[Route("/admin/tools/filesharing", name: "admin.tools.filesharing", methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function index(): Response
    {
        $files = [];
        $finder = (new Finder())->files()->in($this->adminFileSharingDir);
        foreach ($finder->getIterator() as $file) {
            $files[] = [
                'name' => $file->getBasename(),
                'size' => StringUtils::formatBytes($file->getSize()),
                'time' => StringUtils::formatDate($file->getMTime()),
            ];
        }

        return $this->render('admin/tools/filesharing.html.twig', [
            'files' => $files,
        ]);
    }

    #[Route("/admin/tools/filesharing/download/{path}", name: "admin.tools.filesharing.download")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function download(string $path): Response
    {
        return $this->decodePath($this->adminFileSharingDir, $path, fn($file) => $this->createFileDownloadResponse($file), 'admin.tools.filesharing');
    }

    #[Route("/admin/tools/filesharing", name: "admin.tools.filesharing.upload", methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function upload(Request $request): Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('datei');

        try {
            $file->move($this->adminFileSharingDir, $file->getClientOriginalName());
            $this->addFlash('success', sprintf('Die Datei %s wurde heraufgeladen!', $file->getClientOriginalName()));
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Fehler beim Upload! ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin.tools.filesharing');
    }

    #[Route("/admin/tools/filesharing/rename/{path}", name: "admin.tools.filesharing.rename")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function rename(Request $request, string $path): Response
    {
        return $this->decodePath($this->adminFileSharingDir, $path, function ($file) use ($request) {
            if ($request->isMethod('POST')) {
                rename($file, $this->adminFileSharingDir . "/" . $request->request->get('rename'));
                $this->addFlash('success', "Datei wurde umbenannt!");

                return $this->redirectToRoute('admin.tools.filesharing');
            }

            return $this->render('admin/tools/filesharing-rename.html.twig', [
                'name' => basename($file),
            ]);
        }, 'admin.tools.filesharing');
    }

    #[Route("/admin/tools/filesharing/delete/{path}", name: "admin.tools.filesharing.delete")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function remove(string $path): Response
    {
        return $this->decodePath($this->adminFileSharingDir, $path, function ($file) {
            @unlink($file);
            $this->addFlash('success', "Datei wurde gelöscht!");
            return $this->redirectToRoute('admin.tools.filesharing');
        }, 'admin.tools.filesharing');
    }
}
