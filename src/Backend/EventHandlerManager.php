<?php

declare(strict_types=1);

namespace EtoA\Backend;

use EtoA\Core\Configuration\ConfigurationService;
use Exception;

class EventHandlerManager
{
    public const CONFIG_FILE_NAME = 'eventhandler.conf';

    private ConfigurationService $config;

    public function __construct(
        ConfigurationService $config
    ) {
        $this->config = $config;
    }

    public function checkDaemonRunning(): ?int
    {
        if ($fh = @fopen($this->getPidFilePath(), "r")) {
            $pid = intval(fread($fh, 50));
            fclose($fh);
            if ($pid > 0) {
                $cmd = "ps $pid";
                exec($cmd, $output);
                if (count($output) >= 2) {
                    return $pid;
                }
            }
        }

        return null;
    }

    /** @return string[] */
    public function start(): array
    {
        $this->checkFiles();

        $cmd = $this->getExecutable() . " " . $this->getInstanceName() . " -d -c " . $this->getConfigFile() . " -p " . $this->getPidFilePath();
        exec($cmd, $output);

        return $output;
    }

    /** @return string[] */
    public function stop(): array
    {
        $this->checkFiles();

        $cmd = $this->getExecutable() . " " . $this->getInstanceName() . " -d -s -c " . $this->getConfigFile() . " -p " . $this->getPidFilePath();
        exec($cmd, $output);

        return $output;
    }

    private function checkFiles(): void
    {
        $executable = $this->getExecutable();
        if (!file_exists($executable)) {
            throw new Exception("Eventhandler Executable $executable nicht vorhanden!");
        }

        $configFile = $this->getExecutable();
        if (!file_exists($configFile)) {
            throw new Exception("Eventhandler Konfigurationsdatei $configFile nicht vorhanden!");
        }
    }

    private function getPidFilePath(): string
    {
        return getAbsPath($this->config->get('daemon_pidfile'));
    }

    private function getExecutable(): string
    {
        $executable = $this->config->get('daemon_exe');
        if (!$executable) {
            $executable = realpath(RELATIVE_ROOT . '../eventhandler/target/etoad');
        }

        return $executable;
    }

    private function getInstanceName(): string
    {
        return $this->config->get('daemon_instance');
    }

    private function getConfigFile(): string
    {
        return realpath(RELATIVE_ROOT . 'config/' . EventHandlerManager::CONFIG_FILE_NAME);
    }
}
