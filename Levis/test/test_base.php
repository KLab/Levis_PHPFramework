<?php
class TestBase
{
    protected function assert($condition, $message = null)
    {
        if ($condition) return;
        if ($message) $this->error($message);
        debug_print_backtrace();
    }

    protected function log($message)
    {
        echo $message.PHP_EOL;
    }

    protected function error($message)
    {
        echo "[ERROR]$message".PHP_EOL;
    }
}
