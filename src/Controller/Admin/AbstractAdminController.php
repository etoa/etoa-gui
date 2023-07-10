<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Security\Admin\CurrentAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class AbstractAdminController extends AbstractController
{
    protected function getUser(): CurrentAdmin
    {
        $user = parent::getUser();
        if (!$user instanceof CurrentAdmin) {
            throw new AccessDeniedHttpException();
        }

        return $user;
    }

    protected function decodePath(string $baseDirectory, string $encodedPath, callable $callback, string $redirectRoute): Response
    {
        $file = realpath($baseDirectory . '/' . base64_decode($encodedPath, true));
        if (str_starts_with($file, realpath($baseDirectory))) {
            if (is_file($file)) {
                return $callback($file);
            } else {
                $this->addFlash('error', "Datei nicht vorhanden!");
            }
        } else {
            $this->addFlash('error', "Ungültiger Pfad!");
        }
        return $this->redirectToRoute($redirectRoute);
    }

    protected function createFileDownloadResponse($file): StreamedResponse
    {
        return new StreamedResponse(fn() => readfile($file), Response::HTTP_OK, [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => filesize($file),
            'Content-Disposition' => 'attachment; filename="' . basename($file) . '"',
        ]);
    }
}
