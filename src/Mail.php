<?php

declare(strict_types=1);

namespace App;

final class Mail
{
    public function __construct(private int $uid, private $info) {}

    public function getUid(): int
    {
        return $this->uid;
    }

    public function getSize(): int
    {
        return $this?->info[0]?->size;
    }

    public function getFileName(): string
    {
        return sprintf(
            '%s_%s.eml',
            $this->uid,
            substr(
                str_replace(
                    '/',
                    '-',
                    $this->decode($this?->info[0]?->subject)
                ),
                0,
                180
            )
        );
    }

    private function decode(?string $text): string {
        if (!$text) {
            return 'empty_subject';
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
}
