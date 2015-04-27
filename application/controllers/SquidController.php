<?php

class SquidController extends Zend_Controller_Action
{
    
    public function init()
    {
        $this->preDispatch();
    }

    public function indexAction()
    {     
    }
    
    public function startAction()
    {
        $squid = new Application_Model_Squid();
        
        $isOn = $squid->isOn();
        
        if( !$isOn )
            $squid->proxyStart();
        
        $this->view->assign( 'isOn', $isOn );
    }
 
    public function stopAction()
    {
        $squid = new Application_Model_Squid();
        
        $isOn = $squid->isOn();
        $this->view->assign( 'isOn', $isOn );

        if( $isOn )
        {
            $squid->proxyStop();
            while( $squid->isOn() )
            {}
        }
            
    }

    public function reconfigureAction()
    {
        $squid = new Application_Model_Squid();
        
        $isOn = $squid->isOn();
        
        if( $isOn )
            $squid->proxyReconfigure();
        
        $this->view->assign( 'isOn', $isOn );

        $squid = new Application_Model_Squid();
        $squid->makeGlobalConfigFile();
    }

    public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index','login');
        }
    }
 
}

