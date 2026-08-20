<?php

declare(strict_types=1);

namespace EtoA\Backend;

use EtoA\Core\Configuration\ConfigurationService;
use Exception;

class EventHandlerManager
{
    public const CONFIG_FILE_NAME = 'eventhandler.conf';

    public function __construct(
        private readonly ConfigurationService $config,
        private readonly string               $projectDir,
    ) {
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

        $cmd = $this->getExecutable() . " " . $this->getInstanceName() . " -d -k -c " . $this->getConfigFile() . " -p " . $this->getPidFilePath();
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

        $configFile = $this->getConfigFile();
        if (!file_exists($configFile)) {
            throw new Exception("Eventhandler Konfigurationsdatei $configFile nicht vorhanden!");
        }
    }

    private function getPidFilePath(): string
    {
        $pidFile = $this->config->get('daemon_pidfile');

        // an absolute path is taken as is, a relative one is project relative (var/…)
        return str_starts_with($pidFile, '/')
            ? $pidFile
            : $this->projectDir . '/' . $pidFile;
    }

    private function getExecutable(): string
    {
        $executable = $this->config->get('daemon_exe');
        if (!$executable) {
            $executable = realpath($this->projectDir . '/eventhandler/target/etoad');
        }

        return $executable;
    }

    private function getInstanceName(): string
    {
        return $this->config->get('daemon_instance');
    }

    private function getConfigFile(): string
    {
        return realpath($this->projectDir . '/config/' . EventHandlerManager::CONFIG_FILE_NAME);
    }
}
