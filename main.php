<?php

require_once 'vendor/autoload.php';

use App\Imap;
use App\FileSystem;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$fileSystem = new FileSystem($_ENV['BACKUP_FOLDER']);
$client = new Imap($_ENV['IMAP_MAILBOX'], $_ENV['IMAP_USER'], $_ENV['IMAP_PASSWORD']);
$connection = $client->open();

$exitCode = 0;
foreach ($client->fetch($connection) as $mail) {
    $fileName = $mail->getFileName();

    if (true === $fileSystem->fileExists($mail)) {
        echo 'SKIPPING '.$fileName.PHP_EOL;
        continue;
    }

    $result = imap_savebody($connection, $fileSystem->getFilePath($fileName), $mail->getUid());
    if (false === $result) {
        echo 'ERROR Cannot save email '.$fileName.PHP_EOL;
        $exitCode = 1;
    }

    echo 'SUCCESS save email '.$fileName.PHP_EOL;
}

exit($exitCode);
