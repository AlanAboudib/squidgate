<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->preDispatch();
    }

    public function indexAction()
    {
        // action body
    }

    public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index','login');
        }
    }

}





