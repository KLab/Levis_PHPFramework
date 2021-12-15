<?php

class Logger
{
    private static $log_dir = "";
    private static $archive_dir = "";
    private static $log_file_path = "";
    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            $instance = new self;
            $instance->init();
        }
        return $instance;
    }

    private function init()
    {
        self::$log_dir = resolveFilePath(__DIR__, "../logs/");
        self::$archive_dir = self::$log_dir. 'archive/';
        self::$log_file_path = self::$log_dir. date("Y-m-d") . "_logs.txt";
        $this->complessPastFiles();
    }

    public function info($message)
    {
        $this->output("INFO", $message);
    }

    public function warning($message)
    {
        $this->output("WARNING", $message);
    }

    public function error($message)
    {
        $this->output("ERROR", $message);
    }

    private function output($level, $message)
    {
        $pid = getmypid();
        $msg = sprintf("%s:%s [%s]%s".PHP_EOL, $pid, date("Y-m-d H:i:s.u"), $level, $message);
        $result = file_put_contents(self::$log_file_path, $msg, FILE_APPEND | LOCK_EX);
        if (!$result) {
            error_log("Logging failed!!");
        }
    }

    private function complessPastFiles()
    {
        $today_log_file = date("Y-m-d") . "_logs.txt";
        foreach (glob(self::$log_dir. '*_logs.txt') as $file) {
            if (!is_file($file)) continue;
            if (basename($file) === $today_log_file) continue;
            if (file_exists(self::$archive_dir)) {
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
