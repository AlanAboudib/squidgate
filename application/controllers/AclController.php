<?php

class AclController extends Zend_Controller_Action
{

    public function init()
    {
        $this->preDispatch();    
    }

    public function indexAction()
    {
    }

    public function newGroupAclAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You don't have permission to access this page";
            return;
        }

        $newGroupAclForm = new Application_Form_NewGroupAcl();

        if(!isset($_POST))
        {
            $reqParams = $this->getRequest()->getParams();
            $groupName = $reqParams['groupName'];
            $newGroupAclForm->fill($groupName);

            $this->view->newGroupAclForm = $newGroupAclForm;
            return;
        }

        if(!$newGroupAclForm->isValid($_POST))
        {
            $this->view->newGroupAclForm = $newGroupAclForm;
            return;
        }
    

        $squid = new Application_Model_Squid();
        $squid->groupAclManager($newGroupAclForm->getValues());


    }

    public function editGroupAclAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You don't have permission to access this page";
            return;
        }

	$editGroupAclForm = new Application_Form_EditGroupAcl();

        if(!isset($_POST))
        {
            $reqParams = $this->getRequest()->getParams();
            $groupName = $reqParams['groupName'];
            $editGroupAclForm->fill($groupName);

            $this->view->editGroupAclForm = $editGroupAclForm;
            return;
        }

        if(!$editGroupAclForm->isValid($_POST))
        {
            $this->view->editGroupAclForm = $editGroupAclForm;
            return;
        }
    

        $squid = new Application_Model_Squid();
        $squid->groupAclManager($editGroupAclForm->getValues());
    }

    public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index','login');
        }
    }
}





