<?php

declare(strict_types=1);

namespace App;

use App\Exception\ImapOpenException;
use App\Exception\ImapSearchException;
use Generator;

final class Imap
{
    public function __construct(
        private $mailBox,
        private $user,
        private $password,
    ) {}

    /** @return resource */
    public function open()
    {
        $connection = imap_open($this->mailBox, $this->user, $this->password, OP_READONLY);
        if (false === $connection) {
            throw new ImapOpenException();
        }

        return $connection;
    }

    /**
     * @param resource $connection
     *
     * @return Generator
     */
    public function fetch($connection): Generator
    {
        $search = imap_search($connection, 'ALL');
        if (false === $search) {
            throw new ImapSearchException();
        }

        $mailFactory = new MailFactory($connection);
        foreach ($search as $uid) {
            yield $mailFactory($uid);
        }
    }
}
