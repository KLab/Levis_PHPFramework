<?php
declare(strict_types=1);

enum LogLevel: string
{
    case Info = 'INFO';
    case Warning = 'WARNING';
    case Error = 'ERROR';
}

class Logger
{
    private static string $log_dir = "";
    private static string $archive_dir = "";
    private static string $log_file_path = "";

    public static function getInstance(): self
    {
        static $instance;
        if (!$instance) {
            $instance = new self;
            $instance->init();
        }
        return $instance;
    }

    private function init(): void
    {
        self::$log_dir = resolveFilePath(__DIR__, "../logs/");
        self::$archive_dir = self::$log_dir. 'archive/';
        self::$log_file_path = self::$log_dir. date("Y-m-d") . "_logs.txt";
        $this->compressPastFiles();
    }

    public function info(string $message): void
    {
        $this->output(LogLevel::Info, $message);
    }

    public function warning(string $message): void
    {
        $this->output(LogLevel::Warning, $message);
    }

    public function error(string $message): void
    {
        $this->output(LogLevel::Error, $message);
    }

    private function output(LogLevel $level, string $message): void
    {
        $pid = getmypid();
        $msg = sprintf("%s:%s [%s]%s".PHP_EOL, $pid, date("Y-m-d H:i:s.u"), $level->value, $message);
        $result = file_put_contents(self::$log_file_path, $msg, FILE_APPEND | LOCK_EX);
        if (!$result) {
            error_log("Logging failed!!");
        }
    }

    private function compressPastFiles(): void
    {
        $today_log_file = date("Y-m-d") . "_logs.txt";
        foreach (glob(self::$log_dir. '*_logs.txt') as $file) {
            if (!is_file($file)) continue;
            if (basename($file) === $today_log_file) continue;
            if (!file_exists(self::$archive_dir)) {
                mkdir(self::$archive_dir);
            }
            $new_path = $file. ".gz";
            $gz = gzopen($new_path, 'w9');
            if ($gz) {
                gzwrite($gz, file_get_contents($file));
                if (gzclose($gz)) {
                    unlink($file);
                } else {
                    Logger::getInstance()->error("failed close {$new_path}");
                }
            }

        }
    }
}
