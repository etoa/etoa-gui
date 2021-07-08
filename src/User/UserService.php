<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Configuration\ConfigurationService;
use Log;
use Mail;

class UserService
{
    private ConfigurationService $config;
    private UserRepository $userRepository;

    public function __construct(
        ConfigurationService $config,
        UserRepository $userRepository
    ) {
        $this->config = $config;
        $this->userRepository = $userRepository;
    }

    public function removeInactive(bool $manual = false): int
    {
        // Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat
        $registerTime = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));

        // Zeit nach der ein User normalerweise gelöscht wird
        $onlineTime = time() - (24 * 3600 * $this->config->param1Int('user_inactive_days'));

        $inactiveUsers = $this->userRepository->findInactive($registerTime, $onlineTime);
        foreach ($inactiveUsers as $user) {
            $this->userRepository->remove($user->id);
        }

        Log::add(
            Log::F_SYSTEM,
            Log::INFO,
            count($inactiveUsers) . " inaktive User die seit " . date("d.m.Y H:i", $onlineTime) . " nicht mehr online waren oder seit " . date("d.m.Y H:i", $registerTime) . " keine Punkte haben wurden " . ($manual ? 'manuell' : '') . " gelöscht!"
        );

        return count($inactiveUsers);
    }

    public function informLongInactive(): void
    {
        $user_inactive_time_long = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));
        $inactiveTime = time() - (24 * 3600 * $user_inactive_time_long);

        $longInactive = $this->userRepository->findLongInactive($inactiveTime - (3600 * 24), $inactiveTime);
        foreach ($longInactive as $user) {
            $text = "Hallo " . $user->nick . "

Du hast dich seit mehr als " . $this->config->param2Int('user_inactive_days') . " Tage nicht mehr bei Escape to Andromeda (" . $this->config->get('roundname') . ") eingeloggt und
dein Account wurde deshalb als inaktiv markiert. Solltest du dich innerhalb von " . $this->config->getInt('user_inactive_days') . " Tage
nicht mehr einloggen wird der Account gelöscht.

Mit freundlichen Grüssen,
die Spielleitung";

            $mail = new Mail('Inaktivität', $text);
            $mail->send($user->email);
        }
    }
}
