<?php

class GatewayController extends Zend_Controller_Action
{

    private $users = array();

    public function init()
    {
        $this->preDispatch();
    }

    public function indexAction()
    {
    }

    public function generalsettingsAction()
    {
    }

    public function portsAction()
    {
    }

    public function cacheAction()
    {
    }

    public function usersgroupsAction()
    {
    }

    public function monitorAction()
    {
    }

    public function logAction()
    {
    }

    public function newuserAction()
    {
        $newUserForm =  new Application_Form_UserInfo();
        $newUserForm->newUser();

        if( !$this->getRequest()->isPost() )
        {
           $this->view->assign('newUserForm', $newUserForm); //or $this->view->form = $form
           return;
        }

        if( !$newUserForm->isValid( $_POST ) )
        {
           $this->view->assign('newUserForm', $newUserForm);
           return;
        }

// array_push($this->users, implode(" ", array( $_POST['firstName'], $_POST['lastName'])));

        $this->view->newUserForm = $newUserForm;

        $openLDAP = new Application_Model_Ldap();
        $openLDAP->addUser( $newUserForm->getValues() );
    }

    public function newgroupAction()
    {
        
        $newGroupForm = new Application_Form_GroupInfo();
        $newGroupForm->newGroup();

        if( !$this->getRequest()->isPost() )
        {
           $this->view->newGroupForm = $newGroupForm;
           return;
        }

        if( !$newGroupForm->isValid( $_POST ) )
        {
           $this->view->newGroupForm = $newGroupForm;
           return;
        }

        $newGroupFormArray = $newGroupForm->getValues();

        $openLDAP = new Application_Model_Ldap();
        $openLDAP->addGroup( $newGroupFormArray );
 
        $groupsDataRep = new Application_Model_GroupsDataRepository();
        $groupsDataRep->createGroupDirectory($newGroupFormArray['groupName']);       

        unset($_POST);

        $this->_forward('new-group-acl', 
                        'Acl',
                         null,
                         array('groupName' => $newGroupFormArray['groupName']) 
               );
    }

    public function searchUsersAction()
    {
        $searchUsersForm = new Application_Form_SearchUsers();

        $this->view->searchUsersForm = $searchUsersForm;
    }

    public function searchUsersResultsAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You do not have permission to access this page";
            return;
        }

        $searchUsersForm = new Application_Form_SearchUsers();

        if( !$searchUsersForm->isValid( $_POST ) )
        {
            $this->_forward('search-users');
        }
        
        $resultsForm = new Application_Form_SearchUsersResults();
        $resultsForm->setFilter($searchUsersForm->getValues());

        $this->view->resultsForm = $resultsForm;
        
    }

    public function searchUsersResultsHandlerAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You do not have permission to access this page";
            return;
        }      


        if( isset( $_POST['delete'] ) && !isset( $_POST['edit']) )
        {
            $this->_forward('delete-users');
        }

        if( isset( $_POST['edit'] ) && !isset( $_POST['delete']) )
        {
            $userOldDn = $_POST['results'][0];  //the edit is applied only to the first selected group
            unset( $_POST );
            $this->_forward('edit-user', 
                            'Gateway', 
                             null,
                             array( 'userOldDn' => $userOldDn )
                   );
        }
        
    }

    public function deleteUsersAction()
    {
        $ldap = new Application_Model_Ldap();

        foreach($_POST['results'] as $userDn )
            $ldap->deleteEntry( $userDn );
    }

    public function editUserAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You do not have permission to access this page";
            return;
        }
       
        if( !isset($_POST)) 
        {     
            $reqParams = $this->getRequest()->getParams();       
            $userOldDn = $reqParams['userOldDn'];
            $editUserForm = new Application_Form_EditUser(); 
            $editUserForm->fill( $userOldDn );
            $this->view->editUserForm = $editUserForm;

            return;
        }

        $editUserForm = new Application_Form_EditUser();
 
        if( !$editUserForm->isValid($_POST) )
        {
            $this->view->editUserForm = $editUserForm;
            return;
        }
      
        $ldap = new Application_Model_Ldap();
        $ldap->updateUser( $editUserForm->getValues() );
    }



    public function searchgroupsAction()
    {
        $searchGroupsForm = new Application_Form_SearchGroups();
        $searchGroupsForm->search();

        if( !$this->getRequest()->isPost() )
        {
           $this->view->searchGroupsForm = $searchGroupsForm;
           return;
        }

        if( !$searchGroupsForm->isValid( $_POST ) )
        {
           $this->view->searchGroupsForm = $searchGroupsForm;
           return;
        }

    }

    public function searchgroupsresultsAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You do not have permission to access this page";
            return;
        }

        $searchGroupsForm = new Application_Form_SearchGroups();
        $searchGroupsForm->search();

        if( !$searchGroupsForm->isValid( $_POST ) )
        {
            $this->_forward('searchgroups');
        }

        $resultsForm = new Application_Form_SearchGroups();
        $resultsForm->results( $searchGroupsForm->getValue('groupName' ) );

        $this->view->resultsForm = $resultsForm;
    }

    public function searchGroupsResultsHandlerAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You do not have permission to access this page";
            return;
        }      


        if( isset( $_POST['delete'] ) && !isset( $_POST['editInfo']) && !isset($_POST['editAcl']) ) 
        {
            $this->_forward('delete-groups');
        }

        if( isset( $_POST['editInfo'] ) && !isset( $_POST['delete']) && !isset($_POST['editAcl']) )
        {
            $groupOldDn = $_POST['results'][0];  //the edit is applied only to the first selected group
            unset( $_POST );
            $this->_forward('edit-group-info', 
                            'Gateway', 
                             null,
                             array( 'groupOldDn' => $groupOldDn )
                   );
        }

        if( isset( $_POST['editAcl'] ) && !isset( $_POST['delete']) && !isset($_POST['editInfo']) )
        {
            $groupDn = $_POST['results'][0];  //the edit is applied only to the first selected group
            $groupName = substr($groupDn, 3, strpos($groupDn, ',')-3);
            unset( $_POST );
            $this->_forward('edit-group-acl', 
                            'Acl', 
                             null,
                             array( 'groupName' => $groupName )
                   );
        }

    }

    public function editGroupInfoAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You do not have permission to access this page";
            return;
        }
       
        if( !isset($_POST)) 
        {     
            $reqParams = $this->getRequest()->getParams();       
            $groupOldDn = $reqParams['groupOldDn'];
            $editGroupForm = new Application_Form_GroupInfo(); 
            $editGroupForm->populatedEditGroup( $groupOldDn );
            $this->view->editGroupForm = $editGroupForm;

            return;
        }

        $editGroupForm = new Application_Form_GroupInfo();
        $editGroupForm->editGroup();
 
        if( !$editGroupForm->isValid($_POST) )
        {
            $this->view->editGroupForm = $editGroupForm;
            return;
        }
      
        $ldap = new Application_Model_Ldap();
        $ldap->updateGroup( $editGroupForm->getValues() );
    }

    public function deleteGroupsAction()
    {
        $ldap = new Application_Model_Ldap();

        foreach($_POST['results'] as $groupDn )
        {
            $ldap->deleteEntry( $groupDn );
            
            $groupName = substr($groupDn, 3, strpos($groupDn, ',')-3);
            $groupsDataRep = new Application_Model_GroupsDataRepository();
            $groupsDataRep->removeGroupDirectory($groupName);
        }
    }

    public function preDispatch()
    {
        if(!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'login');
        }
    }

}








