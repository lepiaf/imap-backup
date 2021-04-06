<?php

declare(strict_types=1);

namespace Tests;

use App\FileSystem;
use App\Mail;
use PHPUnit\Framework\TestCase;
use stdClass;

class FileSystemTest extends TestCase
{
    private const BACKUP_FOLDER = __DIR__.'/dummy';

    public function testGetFilePath(): void
    {
        $fileSystem = new FileSystem(self::BACKUP_FOLDER);
        self::assertSame(self::BACKUP_FOLDER.'/dummy.me', $fileSystem->getFilePath('dummy.me'));
    }

    public function testFileNotExists(): void
    {
        $item = new stdClass();
        $item->subject = 'hello';
        $item->size = 5;
        $mail = new Mail(1, [$item]);

        $fileSystem = new FileSystem(self::BACKUP_FOLDER);
        self::assertFalse($fileSystem->fileExists($mail));
    }

    public function testFileExists(): void
    {
        $this->prepareTestFile();

        $item = new stdClass();
        $item->subject = 'hello';
        $item->size = 4;
        $mail = new Mail(1, [$item]);

        $fileSystem = new FileSystem(self::BACKUP_FOLDER);
        self::assertTrue($fileSystem->fileExists($mail));
    }

    private function prepareTestFile(): void
    {
        if (is_dir(self::BACKUP_FOLDER)) {
            if (file_exists(self::BACKUP_FOLDER.'/1_hello.eml')) {
                unlink(self::BACKUP_FOLDER.'/1_hello.eml');
            }
            rmdir(self::BACKUP_FOLDER);
        }
        mkdir(self::BACKUP_FOLDER);
        file_put_contents(self::BACKUP_FOLDER.'/1_hello.eml', 'data');
    }
}
