<?php
require_once('libs/helper.php');
require_once('libs/bootstrap.php');

class Router
{
    /** @var Controller */
    private $controller;

    public function __construct()
    {
        /** @var Logger */
        $logger = Logger::getInstance();
        try {
            if (!isset($_SERVER['PATH_INFO'])) {
                $this->renderWeb('index.twig');
                return;
            }
            $infos = explode('/', $_SERVER['PATH_INFO']);
            array_shift($infos);
            /** @var bool */
            $is_api = $infos[0] === 'api';
            AutoLoader::setIsApi($is_api);
            $this->controller = $this->createController($is_api ? $infos[1] : $infos[0]);
            /** @var string */
            $action = $this->getAction($infos, $is_api);
            $this->controller->$action();
            if ($is_api) {
                $this->executeApi();
            } else {
                $controller = $infos[0];
                $this->renderWeb("$controller/$action.twig");
            }
        } catch (Exception $e) {
            $logger->error($e->getMessage());
            throw $e;
        }
    }

    private function createController(string $input): Controller
    {
        $controller_name = $input . '_controller';
        $controller = camelize($controller_name);
        return new $controller();
    }

    private function getAction(array $infos, bool $is_api): string
    {
        if ($is_api) {
            return count($infos) > 2 ? $infos[2] : 'index';
        }
        return count($infos) > 0 ? $infos[1] : 'index';
    }

    private function executeApi()
    {
        echo json_encode($this->controller->getVars());
    }

    private function renderWeb(string $view_path)
    {
        require_once('./vendor/autoload.php');
        require_once('./libs/twig_extension.php');
        $loader = new Twig\Loader\FilesystemLoader(__DIR__ . '/cms/view');
        $twig = new Twig\Environment($loader);
        $twig->addExtension(new TwigExteinsion());
        $params = $this->controller ? $this->controller->getVars() : [];
        $params['has_notification'] = Session::exists('notification_message');
        if ($params['has_notification']) {
            $params['notification_level'] = Session::get('notification_level');
            $params['notification_message'] = Session::get('notification_message');
            session_destroy();
        }
        echo $twig->render($view_path, $params);
    }
}

new Router();
