<?php

namespace EtoA\Controller\External;

use EtoA\Controller\AbstractLegacyShowController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractLegacyShowController
{
    protected ?string $pageTitle = 'Einloggen';

    #[Route('/login', name: 'external.login')]
    public function index(Request $request): Response
    {
        $loginUrl = $this->config->get('loginurl');
        if ($loginUrl) {
            return $this->redirect($loginUrl);
        }

        return $this->handle(function () use ($loginUrl, $request) {

            // Login if requested
            if ($request->request->has('login')) {
                $loginNick = trim($request->request->get('nickname', ''));
                $loginPassword = trim($request->request->get('password', ''));
                if (!$this->userSession->login($loginNick, $loginPassword)) {
                    $this->addFlash('error', $this->userSession->getLastError());
                    return $this->redirectToRoute('external.login');
                }
                return $this->redirect('/'); // TODO
            }

            if ($request->query->has('err')) {
                $errCode = $request->query->get('err');
                $this->addFlash('error', $this->getErrMsg($errCode));
            }

            // TODO CSRF token

            return $this->render('external/login.html.twig', [
                'loginUrl' => $loginUrl,
                'roundName' => $this->config->get('roundname'),
            ]);
        });
    }

    private function getErrMsg(string $err): string
    {
        return match ($err) {
            "name" => "Du hast vergessen einen Namen oder ein Passwort einzugeben!",
            "pass" => "Falsches Passwort oder falscher Benutzername!",
            "ip" => "IP-Adresse-Überprüfungsfehler! Kein Login von diesem Computer möglich, da schon eine andere IP mit diesem Account verbunden ist!",
            "timeout" => "Das Timeout wurde erreicht und du wurdest automatisch ausgeloggt!",
            "session" => "Session-Cookie-Fehler. Überprüfe ob dein Browser wirklich Sitzungscookies akzeptiert!",
            "tomanywindows" => "Es wurden zu viele Fenster geöffnet oder aktualisiert, dies ist leider nicht erlaubt!",
            "session2", "nosession" => "Deine Session ist nicht mehr vorhanden! Sie wurde entweder gelöscht oder sie ist fehlerhaft. Dies kann passieren wenn du dich an einem anderen PC einloggst obwohl du noch mit diesem online warst!",
            "verification" => "Falscher Grafikcode! Bitte gib den linksstehenden Code in der Grafik korrekt in das Feld darunter ein!
            Diese Massnahme ist leider nötig um das Benutzen von automatisierten Programmen (Bots) zu erschweren.",
            "logintimeout" => "Der Login-Schlüssel ist abgelaufen! Bitte logge dich neu ein!",
            "sameloginkey" => "Der Login-Schlüssel wurde bereits verwendet! Bitte logge dich neu ein!",
            "wrongloginkey" => "Falscher Login-Schlüssel! Ein Login ist nur von der offiziellen EtoA-Startseite aus möglich!",
            "nologinkey" => "Kein Login-Schlüssel! Ein Login ist nur von der offiziellen EtoA-Startseite aus möglich!",
            "general" => "Ein allgemeiner Fehler ist aufgetreten. Bitte den Entwickler kontaktieren!",
            default => "Unbekannter Fehler (" . $err . "). Bitte den Entwickler kontaktieren!",
        };
    }
}