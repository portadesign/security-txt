<?php

declare(strict_types=1);

namespace Portadesign\SecurityTxt;

final class SecurityTxt
{
    public const DEFAULT_FILE = 'public/.well-known/security.txt';

    public const DEFAULT_CONTACT = 'mailto:security@portadesign.cz';

    public const DEFAULT_LANGUAGES = 'cs, en';

    public const RENEW_MONTHS = 11;

    public function __construct(private readonly string $file)
    {
    }

    public function create(string $host, string $contact, string $languages, bool $force): string
    {
        if (is_file($this->file) && !$force) {
            throw new \RuntimeException(sprintf('%s exists, use --force to overwrite', $this->file));
        }

        $content = implode("\n", [
            'Contact: ' . $contact,
            'Expires: ' . $this->nextExpiry(),
            'Preferred-Languages: ' . $languages,
            'Canonical: https://' . $host . '/.well-known/security.txt',
        ]) . "\n";

        $this->write($content);

        return $content;
    }

    public function daysUntilExpiry(): int
    {
        if (!is_file($this->file)) {
            throw new \RuntimeException(sprintf('%s missing, run: security-txt init <host>', $this->file));
        }

        $expiresLine = $this->expiresLine();
        $expires = \DateTimeImmutable::createFromFormat(DATE_RFC3339_EXTENDED, $expiresLine)
            ?: \DateTimeImmutable::createFromFormat(DATE_RFC3339, $expiresLine);

        if (!$expires instanceof \DateTimeImmutable) {
            throw new \RuntimeException(sprintf('Expires is not RFC 3339: %s', $expiresLine));
        }

        return (int) (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->diff($expires)->format('%r%a');
    }

    public function renew(): string
    {
        $this->expiresLine();
        $expiry = $this->nextExpiry();
        $content = preg_replace('/^Expires:.*$/m', 'Expires: ' . $expiry, (string) file_get_contents($this->file), 1);
        $this->write((string) $content);

        return $expiry;
    }

    private function expiresLine(): string
    {
        if (!is_file($this->file)) {
            throw new \RuntimeException(sprintf('%s missing, run: security-txt init <host>', $this->file));
        }

        if (!preg_match('/^Expires:\s*(\S+)\s*$/m', (string) file_get_contents($this->file), $match)) {
            throw new \RuntimeException(sprintf('%s has no Expires line', $this->file));
        }

        return $match[1];
    }

    private function nextExpiry(): string
    {
        return (new \DateTimeImmutable('first day of next month midnight', new \DateTimeZone('UTC')))
            ->modify(sprintf('+%d months', self::RENEW_MONTHS - 1))
            ->format('Y-m-d\TH:i:s.000\Z');
    }

    private function write(string $content): void
    {
        $directory = dirname($this->file);

        if (!is_dir($directory) && !mkdir($directory, 0775, true)) {
            throw new \RuntimeException(sprintf('cannot create %s', $directory));
        }

        file_put_contents($this->file, $content);
    }
}
