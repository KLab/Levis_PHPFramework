<?php
declare(strict_types=1);

class Controller
{
    public const string APP_URL = "localhost/index.php";

    private array $vars = [];
    private array $notification = [];

    public function getVars(): array { return $this->vars; }
    public function hasNotification(): bool { return !empty($this->notification); }
    public function GetNotificationLevel(): string { return $this->notification['level']; }
    public function getNotificationMessage(): string { return $this->notification['message']; }

    public function set(string|array $name, mixed $value = null): void
    {
        if (is_array($name)) {
            foreach($name as $k => $v) {
                $this->vars[$k] = $v;
            }
            return;
        }
        $this->vars[$name] = $value;
    }

    public function redirect(string $url, array $params = []): never
    {
        $query = http_build_query($params);
        if (strlen($query) > 0) {
            $query = '?' . $query;
        }
        if ($url === '/') {
            $url = self::APP_URL . $query;
        } elseif (str_starts_with($url, 'http')) {
            $url = $url . $query;
        } else {
            $url = self::APP_URL . $url . $query;
        }
        header('Location: ' . $url);
        exit;
    }

    public function setNotification(string $message, string $level): void
    {
        Session::set('notification_level', $level);
        Session::set('notification_message', $message);
    }
}
