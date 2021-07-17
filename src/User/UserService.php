<?php

declare(strict_types=1);

namespace EtoA\User;

use BackendMessage;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Planet\PlanetRepository;
use Log;
use Mail;

class UserService
{
    private ConfigurationService $config;
    private UserRepository $userRepository;
    private PlanetRepository $planetRepository;
    private BuildingRepository $buildingRepository;
    private TechnologyRepository $technologyRepository;
    private ShipRepository $shipRepository;
    private DefenseQueueRepository $defenseQueueRepository;

    public function __construct(
        ConfigurationService $config,
        UserRepository $userRepository,
        PlanetRepository $planetRepository,
        BuildingRepository $buildingRepository,
        TechnologyRepository $technologyRepository,
        ShipRepository $shipRepository,
        DefenseQueueRepository $defenseRepository
    ) {
        $this->config = $config;
        $this->userRepository = $userRepository;
        $this->planetRepository = $planetRepository;
        $this->buildingRepository = $buildingRepository;
        $this->technologyRepository = $technologyRepository;
        $this->shipRepository = $shipRepository;
        $this->defenseQueueRepository = $defenseRepository;
    }

    public function removeInactive(bool $manual = false): int
    {
        /** @var int $registerTime Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat */
        $registerTime = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));

        /** @var int $onlineTime Zeit nach der ein User normalerweise gelöscht wird */
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
        $userInactiveTimeLong = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));
        $inactiveTime = time() - (24 * 3600 * $userInactiveTimeLong);

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

    public function getNumInactive(): int
    {
        /** @var int $registerTime Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat */
        $registerTime = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));

        /** @var int $onlineTime Zeit nach der ein User normalerweise gelöscht wird */
        $onlineTime = time() - (24 * 3600 * $this->config->param1Int('user_inactive_days'));

        $inactiveUsers = $this->userRepository->findInactive($registerTime, $onlineTime);

        return count($inactiveUsers);
    }

    public function removeDeleted(bool $manual = false): int
    {
        $deletedUsers = $this->userRepository->findDeleted();
        foreach ($deletedUsers as $user) {
            $this->userRepository->remove($user->id);
        }

        Log::add(
            Log::F_SYSTEM,
            Log::INFO,
            count($deletedUsers) . ' als gelöscht markierte User wurden ' . ($manual ? 'manuell' : '') . ' gelöscht!'
        );

        return count($deletedUsers);
    }

    public function setUmodToInactive(): int
    {
        $threshold = time() - ($this->config->param1Int('hmode_days') * 86400);
        $users = $this->userRepository->findInactiveInHolidayMode($threshold);
        foreach ($users as $user) {
            $hmodTime = time() - $user->hmodFrom;

            $this->userRepository->disableHolidayMode($user->id);

            $newLogoutTime = time() - ($this->config->param2Int('user_inactive_days') * 86400);
            $this->userRepository->setLogoutTime($user->id, $newLogoutTime);

            $buildingItems = $this->buildingRepository->findForUser($user->id);
            foreach ($buildingItems as $item) {
                if ($item->startTime == 0 || $item->endTime == 0) {
                    continue;
                }
                $item->buildType = 3;
                $item->startTime += $hmodTime;
                $item->endTime += $hmodTime;
                $this->buildingRepository->save($item);
            }

            $technologyItems = $this->technologyRepository->findForUser($user->id);
            foreach ($technologyItems as $item) {
                if ($item->startTime == 0 || $item->endTime == 0) {
                    continue;
                }
                $item->buildType = 3;
                $item->startTime += $hmodTime;
                $item->endTime += $hmodTime;
                $this->technologyRepository->save($item);
            }

            $shipQueueItems = $this->shipRepository->findQueueItemsForUser($user->id);
            foreach ($shipQueueItems as $item) {
                $item->buildType = 0;
                $item->startTime += $hmodTime;
                $item->endTime += $hmodTime;
                $this->shipRepository->saveQueueItem($item);
            }

            $defQueueItems = $this->defenseQueueRepository->findQueueItemsForUser($user->id);
            foreach ($defQueueItems as $item) {
                $item->buildType = 0;
                $item->startTime += $hmodTime;
                $item->endTime += $hmodTime;
                $this->defenseQueueRepository->saveQueueItem($item);
            }

            $this->userRepository->addSpecialistTime($user->id, $hmodTime);

            $userPlanets = $this->planetRepository->getUserPlanets($user->id);
            foreach ($userPlanets as $planet) {
                $this->planetRepository->setLastUpdated($planet->id, time());
                BackendMessage::updatePlanet($planet->id);
            }
        }

        return count($users);
    }
}
