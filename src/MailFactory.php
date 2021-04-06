<?php

declare(strict_types=1);

namespace App;

final class MailFactory
{
    /** @param resource $connection */
    public function __construct(private $connection) {}

    public function __invoke(int $uid): Mail
    {
        $info = imap_fetch_overview($this->connection, (string) $uid);

        return new Mail($uid, $info);
    }
}
