<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\User\UserRepository;
use EtoA\User\UserToXml;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UsersXmlController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserToXml      $userToXml,
    )
    {
    }

    #[Route('/admin/users/xml', name: 'admin.users.xml')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function list(Request $request): Response
    {
        if ($request->request->has('exportdl')) {
            return $this->redirectToRoute('admin.users.xml.generate', ['id' => $request->request->getInt('export_user_id')]);
        }

        if ($request->request->has('exportcache')) {
            try {
                $xmlfile = $this->userToXml->toCacheFile($request->request->getInt('export_user_id'));
                $this->addFlash('success', "Die Userdaten wurden nach " . $xmlfile . " exportiert.");
            } catch (\Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }
        }

        $xmlFiles = [];
        $files = Finder::create()
            ->in([$this->userToXml->getDataDirectory()])
            ->files()
            ->name('*.xml')
            ->sortByModifiedTime()
            ->reverseSorting();

        foreach ($files as $file) {
            $xmlFiles[] = [
                'basename' => $file->getBasename(),
                'xml' => simplexml_load_file((string)$file->getRealPath()),
                'base64' => base64_encode($file->getBasename()),
            ];
        }

        return $this->render('admin/user-xml/list.html.twig', [
            'xmlFiles' => $xmlFiles,
            'userNicks' => $this->userRepository->searchUserNicknames(),
        ]);
    }

    #[Route('/admin/users/xml/{file}/details', name: 'admin.users.xml.details')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function details(string $file): Response
    {
        $file = $this->userToXml->getDataDirectory() . "/" . base64_decode($file, true);
        if (!is_file($file)) {
            $this->addFlash('error', 'File existiert nicht');

            return $this->redirectToRoute('admin.users.xml');
        }

        return $this->render('admin/user-xml/details.html.twig', [
            'xml' => simplexml_load_file($file),
        ]);
    }

    #[Route('/admin/users/xml/{id}/generate', name: 'admin.users.xml.generate')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function generate(int $id): Response
    {
        $data = $this->userToXml->generate($id);
        if ($data !== '') {
            $filename = sprintf('user_%s.xml', $id);
            $response = new Response($data);
            $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            ));

            return $response;
        }

        $this->addFlash('error', 'XML export fehlgeschlagen');

        return $this->redirectToRoute('admin.users.xml');
    }

    #[Route('/admin/users/xml/{file}/download', name: 'admin.users.xml.download')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function download(string $file): BinaryFileResponse
    {
        $filePath = $this->userToXml->getDataDirectory() . '/' . base64_decode($file, true);
        $response = new BinaryFileResponse(new File($filePath));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }
}
