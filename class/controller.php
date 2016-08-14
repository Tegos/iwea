<?php


class Controller extends Helper
{

    var $files;
    var $path;
    var $arg;
    var $model;
    private $method;


    public function __construct($path = '', $arg = array(), $return = false)
    {
        $this->files = new File;
        $this->method = $path;
        $this->arg = $arg;

        $this->model = new  Model();

        if (strlen($path) > 0)
            $this->route($return);
        else
            if (isset($_GET['action']) && !empty($_GET['action'])) {
                $action = $_GET['action'];
                $this->routePage($action);
            } else {
                $this->routePage('home');
            }
    }

    public function route($return)
    {
        $rez = array();
        if (method_exists($this->model, $this->method)) {
            $rez = json_encode($this->model->{$this->method}());
        }

        if ($return) {
            return $rez;
        } else {
            echo $rez;
        }
    }


    public function routePage($action)
    {
        $action = strtolower($action);
        session_start();
        $view = new Template();
        $act = new Action($this->model);
        $act->header($view);

        switch ($action) {

            case 'home':
                $render_page = $action;
                $act->home($view);
                break;

            case 'search':
                $render_page = $action;
                $act->search_page($view);
                break;

            case 'info':
                $render_page = $action;
                $act->$action($view);
                break;

            case 'all':
                $render_page = $action;
                $act->$action($view);
                break;

            case 'analytics':
                $render_page = $action;
                $act->$action($view);
                break;

            case 'auth_reg':
                $render_page = $action;
                $act->$action($view);
                break;

            case 'reg':
                $render_page = $action;
                $act->$action($view);
                break;

            case 'set_city_id':
                $city_id = $_GET['city_id'];
                setcookie('city_id', (int)$city_id);
                header("Location: /");
                break;

            case 'registration':
                $render_page = $action;
                $act->$action($view);
                break;

            case 'auth':
                $render_page = $action;
                $act->$action($view);
                break;

            case 'set_site_id':
                $site_id = $_GET['site_id'];
                $this->var_dump($site_id);
                //die();
                setcookie('site_id', (int)$site_id);
                usleep(10);
                header("Location: /");
                break;

            default:
                $render_page = 'home';
                $act->home($view);
                break;
        }



        $view->header = $view->render('header');
        $view->footer = $view->render('footer');

        if (isset($render_page)) {
            echo $view->render($render_page);
        }
    }


}