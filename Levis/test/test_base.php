<?php
declare(strict_types=1);

class TestBase
{
    protected function assert(bool $condition, ?string $message = null): void
    {
        if ($condition) return;
        if ($message) $this->error($message);
        debug_print_backtrace();
    }

    protected function log(string $message): void
    {
        echo $message.PHP_EOL;
    }

    protected function error(string $message): void
    {
        echo "[ERROR]$message".PHP_EOL;
    }
}
