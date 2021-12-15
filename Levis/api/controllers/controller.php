<?php

class Controller
{
    const APP_URL = "localhost/index.php";

    private $vars = [];
    private $notification = [];

    public function getVars() { return $this->vars; }
    public function hasNotification(){ return !empty($this->notification); }
    public function GetNotificationLevel() { return $this->notification['level']; }
    public function getNotificationMessage() { return $this->notification['message']; }

    public function set($name, $value = null)
    {
        if (is_array($name)) {
            foreach($name as $k => $v) {
                $this->vars[$k] = $v;
            }
            return;
        }
        $this->vars[$name] = $value;
    }

    public function redirect($url, $params = array())
    {
        $query = http_build_query($params);
        if (strlen($query) > 0) {
            $query = '?' . $query;
        }
        if ($url === '/') {
            $url = self::APP_URL . $query;
        } elseif (strpos($url, 'http') === 0) {
            $url = $url . $query;
        } else {
            $url = self::APP_URL . $url . $query;
        }
        header('Location: ' . $url);
        exit;
    }

    public function setNotification($message, $level)
    {
        Session::set('notification_level', $level);
        Session::set('notification_message', $message);
    }
}
