<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Core\UserType;
use EtoA\User\UserRepository;
use EtoA\User\UserToXml;
use EtoA\User\UserXmlRestoreService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        private readonly UserRepository       $userRepository,
        private readonly UserToXml            $userToXml,
        private readonly UserXmlRestoreService $userXmlRestoreService,
    )
    {
    }

    #[Route('/admin/users/xml', name: 'admin.users.xml', priority: 10)]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function list(Request $request): Response
    {
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

        $form = $this->createFormBuilder()
            ->add('exportdl', SubmitType::class, [
                'label' => 'Herunterladen',
            ])
            ->add('exportcache', SubmitType::class, [
                'label' => 'Exportieren',
            ])
            ->add('user', UserType::class, [
                'required' => true,
                'placeholder' => false,
                'label' => false,
            ])
            ->getForm()
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if ($form->get('exportdl')->isClicked()) {
                return $this->redirectToRoute('admin.users.xml.generate', ['id' => $form->get('user')->getData()->getId()]);
            }

            if ($form->get('exportcache')->isClicked()) {
                try {
                    $xmlfile = $this->userToXml->toCacheFile($form->get('user')->getData());
                    $this->addFlash('success', "Die Userdaten wurden nach " . $xmlfile . " exportiert.");
                } catch (\Exception $ex) {
                    $this->addFlash('error', $ex->getMessage());
                }
            }
        }

        return $this->render('admin/user-xml/list.html.twig', [
            'xmlFiles' => $xmlFiles,
            'userNicks' => $this->userRepository->searchUserNicknames(),
            'form' => $form
        ]);
    }

    #[Route('/admin/users/xml/{file}/details', name: 'admin.users.xml.details')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function details(string $file): Response
    {
        $filePath = $this->userToXml->getDataDirectory() . "/" . base64_decode($file, true);
        if (!is_file($filePath)) {
            $this->addFlash('error', 'File existiert nicht');

            return $this->redirectToRoute('admin.users.xml');
        }

        return $this->render('admin/user-xml/details.html.twig', [
            'xml' => simplexml_load_file($filePath),
            'file' => $file,
        ]);
    }

    #[Route('/admin/users/xml/{file}/restore', name: 'admin.users.xml.restore', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function restore(string $file, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('restore_user_xml_' . $file, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Ungültiges Sicherheits-Token.');

            return $this->redirectToRoute('admin.users.xml.details', ['file' => $file]);
        }

        $filePath = $this->userToXml->getDataDirectory() . "/" . base64_decode($file, true);
        if (!is_file($filePath)) {
            $this->addFlash('error', 'File existiert nicht');

            return $this->redirectToRoute('admin.users.xml');
        }

        try {
            $result = $this->userXmlRestoreService->restore($filePath);
        } catch (Exception $ex) {
            $this->addFlash('error', 'Wiederherstellung fehlgeschlagen: ' . $ex->getMessage());

            return $this->redirectToRoute('admin.users.xml.details', ['file' => $file]);
        }

        return $this->render('admin/user-xml/restore-result.html.twig', [
            'result' => $result,
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
