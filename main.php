<?php

function decode($text) {
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


$mbox = imap_open("{ssl0.ovh.net:993/imap/ssl}", $username, $password, OP_READONLY);
$mails = imap_search($mbox, 'ALL');
foreach ($mails as $mail) {
    $info = imap_fetch_overview($mbox, $mail);
    $fileName = sprintf('%s_%s.eml', $info[0]->uid, substr(str_replace('/', '-', decode($info[0]->subject)), 0, 180));

    if (file_exists('data/'.$fileName)) {
        echo $fileName.' skipping'.PHP_EOL;
        continue;
    }
    echo $fileName.PHP_EOL;
    imap_savebody($mbox, 'data/'.$fileName, $mail);
}

