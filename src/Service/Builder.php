<?php

namespace StackPress\Service;

use StackPress\Config\Config;

class Builder {
    private string $viewsPath;
    private string $dataPath;
    private string $buildPath;

    public function __construct() {
        $config = Config::getInstance();
        $this->viewsPath = $config->getViewsPath();
        $this->dataPath = $config->getDataPath();
        $this->buildPath = $config->getBuildPath();
    }

    public function execute(): void {
        $this->ensureBuildDirectoryExists();
        $data = $this->loadAllData();
        $views = $this->getViewFiles();

        foreach ($views as $viewFile) {
            $viewName = basename($viewFile, '.php');
            $this->renderView($viewFile, $data, $viewName);
        }
    }

    private function ensureBuildDirectoryExists(): void {
        if (!is_dir($this->buildPath)) {
            mkdir($this->buildPath, 0755, true);
        }
    }

    private function loadAllData(): array {
        $data = [];
        $dataFiles = glob($this->dataPath . '/*.json');

        foreach ($dataFiles as $dataFile) {
            $dataName = basename($dataFile, '.json');
            $json = file_get_contents($dataFile);
            $data[$dataName] = json_decode($json, true);
        }

        return $data;
    }

    private function getViewFiles(): array {
        return glob($this->viewsPath . '/*.php');
    }

    private function renderView(string $viewFile, array $data, string $outputName): void {
        ob_start();
        extract($data);
        require $viewFile;
        $content = ob_get_clean();

        $outputFile = $this->buildPath . '/' . $outputName . '.html';
        file_put_contents($outputFile, $content);
    }
}
