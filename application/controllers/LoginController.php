<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        $layout = $this->_helper->layout();
        $layout->setLayout('login');

        $this->preDispatch();
    }

    public function indexAction()
    {
        $loginForm = new Application_Form_Login();
        $this->view->loginForm = $loginForm;
    }

    public function processAction()
    {

        if (!$this->getRequest()->isPost()) {
            return $this->_helper->redirector('index');
        }

        $loginForm = new Application_Form_Login();
        if (!$loginForm->isValid($_POST)) {
            $this->view->loginForm = $loginForm;
            return;
        }

        // Get our authentication adapter and check credentials
        $adapter = $this->getAuthAdapter($loginForm->getValues());
        $auth    = Zend_Auth::getInstance();
        $result  = $auth->authenticate($adapter);
        if (!$result->isValid()) {
            // Invalid credentials
            $loginForm->setDescription('Invalid credentials provided');
            $this->view->loginForm = $loginForm;
            return $this->render('index'); // re-render the login form
        }

        // We're authenticated! Redirect to the home page
        $layout = $this->_helper->layout();
        $layout->setLayout('login');

        $this->_helper->redirector('index', 'index');

    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index'); // back to login page
    }

    public function getAuthAdapter(array $loginForm)
    {
        $adminId = $loginForm['adminId'];
        $ldap = new Application_Model_Ldap();
        $adminDn = $ldap->getAdminDn($adminId);

        $password = $loginForm['password'];

        $ldapOptions = array(
                               'host'              => 'squidgate.local',
                               'username'          => 'CN=Manager,DC=squidgate,DC=local',
                               'password'          => 'squid',
                               'bindRequiresDn'    =>  true,
                               'baseDn'            => 'OU=Admins,DC=squidgate,DC=local'
                       );

        $options = array();
        $options['ldapOptions'] = $ldapOptions;

        return new Zend_Auth_Adapter_Ldap($options, $adminDn, $password);   
    }

    public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName()) {
                $layout = $this->_helper->layout();
                $layout->setLayout('login');
 
                $this->_helper->redirector('index', 'index');
            }
        } else {
            // If they aren't, they can't logout, so that action should 
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index');
            }
        }

    }

}





