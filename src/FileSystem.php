<?php

declare(strict_types=1);

namespace App;

final class FileSystem
{
    public function __construct(private string $backupFolder) {}

    public function getFilePath(string $fileName): string {
        return $this->backupFolder . DIRECTORY_SEPARATOR . $fileName;
    }

    public function fileExists(Mail $mail): bool
    {
        $mailFile = $this->getFilePath($mail->getFileName());

        return file_exists($mailFile) && $mail->getSize() === filesize($mailFile);
    }
}
