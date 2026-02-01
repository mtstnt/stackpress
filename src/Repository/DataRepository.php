<?php

namespace StackPress\Repository;

use Exception;

abstract class DataRepository {
    protected string $dataPath;
    protected string $filename;

    public function __construct(string $dataPath, string $filename) {
        $this->dataPath = $dataPath;
        $this->filename = $filename;
    }

    protected function getFilePath(): string {
        return $this->dataPath . '/' . $this->filename;
    }

    protected function loadData(): array {
        $file = $this->getFilePath();
        if (!file_exists($file)) {
            return [];
        }

        $json = file_get_contents($file);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON data: ' . json_last_error_msg());
        }

        return $data ?? [];
    }

    protected function saveData(array $data): void {
        $file = $this->getFilePath();
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            throw new Exception('Failed to encode data to JSON');
        }

        $result = file_put_contents($file, $json);

        if ($result === false) {
            throw new Exception('Failed to write data to file');
        }
    }

    protected function ensureDirectoryExists(): void {
        if (!is_dir($this->dataPath)) {
            if (!mkdir($this->dataPath, 0755, true)) {
                throw new Exception('Failed to create data directory');
            }
        }
    }
}
