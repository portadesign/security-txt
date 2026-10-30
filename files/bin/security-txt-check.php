#!/usr/bin/env php
<?php declare(strict_types=1);

$file = dirname(__DIR__) . '/public/.well-known/security.txt';
$minDays = (int) ($argv[1] ?? 30);

if (!is_file($file)) {
    fwrite(STDERR, "security.txt missing: {$file}\n");
    exit(1);
}

if (!preg_match('/^Expires:\s*(\S+)\s*$/m', (string) file_get_contents($file), $m)) {
    fwrite(STDERR, "security.txt has no Expires line\n");
    exit(1);
}

$expires = \DateTimeImmutable::createFromFormat(DATE_RFC3339_EXTENDED, $m[1])
    ?: \DateTimeImmutable::createFromFormat(DATE_RFC3339, $m[1]);

if (!$expires instanceof \DateTimeImmutable) {
    fwrite(STDERR, "security.txt Expires is not RFC 3339: {$m[1]}\n");
    exit(1);
}

$days = (int) (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->diff($expires)->format('%r%a');

if ($days < $minDays) {
    fwrite(STDERR, sprintf("security.txt expires in %d day(s) (%s); renew with: %s\n", $days, $m[1], 'bin/security-txt-renew.sh'));
    exit(1);
}

echo sprintf("security.txt ok, expires in %d day(s)\n", $days);
