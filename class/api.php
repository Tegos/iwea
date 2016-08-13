<?php

class Api
{

    var $checkAccess = false;
    var $methodAccess = false;
    var $mobileAccess = false;
    var $arg;
    var $method;

    public function __construct($return = false)
    {
        $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';

        if ($method != '') {
            $data_arguments = array();

            foreach ($_POST as $key => $value) {
                if (!isset($value)) {
                    $value = '';
                }
                $data_arguments[$key] = $value;
            }

            $this->arg = $data_arguments;
            $this->method = $method;
        }

        $this->methodAccess = true;
        $this->checkAccess = true;
        $this->check($return);
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    // is Ok
    public function check($return)
    {
        if ($this->methodAccess && $this->checkAccess) {
            new Controller($this->method, $this->arg, $return);
        }
        return false;
    }
}
