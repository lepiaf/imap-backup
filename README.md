# imap-backup
Tool to backup email from IMAP server

# Basic usage

Update `.env` file.

Run

```
php main.php
```

# Docker use 

```
docker build -t imap-backup .
docker run --env-file .env.local --rm -v $(pwd)/data:/opt/data imap-backup php main.php
```
