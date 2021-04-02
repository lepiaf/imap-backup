<?php

namespace App;

require_once 'vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$mbox = imap_open($_ENV['IMAP_MAILBOX'], $_ENV['IMAP_USER'], $_ENV['IMAP_PASSWORD'], OP_READONLY);
if (false === $mbox) {
    echo 'Cannot open mailbox.'.PHP_EOL;
    exit(1);
}

$mails = imap_search($mbox, 'ALL');
if (false === $mails) {
    echo 'Cannot get email list.'.PHP_EOL;
    exit(1);
}

foreach ($mails as $mail) {
    $info = imap_fetch_overview($mbox, $mail);
    $fileName = getFileName($info);

    if (true === file_exists(getPath($fileName)) && $info[0]->size === filesize(getPath($fileName))) {
        echo 'SKIPPING '.$fileName.PHP_EOL;
        continue;
    }

    echo $fileName.PHP_EOL;
    $result = imap_savebody($mbox, getPath($fileName), $mail);
    if (false === $result) {
        echo 'ERROR Cannot save email '.$fileName.PHP_EOL;
    }
}

function decode(?string $text): string {
    if (!$text) {
        return '<empty subject>';
    }

    $elements = imap_mime_header_decode($text);
    if (count($elements) === 0) {
        return imap_utf8($text);
    }

    if ($elements[0]->charset === 'iso-8859-1') {
        return mb_convert_encoding($elements[0]->text, 'UTF-8', 'iso-8859-1');
    }

    return imap_utf8($text);
}

function getPath(string $fileName): string {
    return $_ENV['BACKUP_FOLDER'] . DIRECTORY_SEPARATOR . $fileName;
}

function getFileName(array $info): string {
    return sprintf(
        '%s_%s.eml',
        $info[0]->uid,
        substr(
            str_replace(
                '/',
                '-',
                decode($info[0]->subject)
            ),
            0,
            180
        )
    );
}
