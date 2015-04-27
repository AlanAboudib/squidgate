<?php

class AdministrationController extends Zend_Controller_Action
{

    public function init()
    {
        $this->preDispatch();   
    }

    public function indexAction()
    {
    }

    public function adminsAndGroupsAction()
    {
    }

    public function newAdminAction()
    {
        $newAdminForm =  new Application_Form_NewAdmin();

        if( !$this->getRequest()->isPost() )
        {
           $this->view->newAdminForm = $newAdminForm;
           return;
        }

        if( !$newAdminForm->isValid( $_POST ) )
        {
           $this->view->newAdminForm = $newAdminForm;
           return;
        }

        $this->view->newAdminForm = $newAdminForm;

        $openLDAP = new Application_Model_Ldap();
        $openLDAP->addAdmin( $newAdminForm->getValues() );

    }

    public function newAdminGroupAction()
    {
        $newAdminGroupForm = new Application_Form_NewAdminGroup();

        if( !$this->getRequest()->isPost() )
        {
           $this->view->newAdminGroupForm = $newAdminGroupForm;
           return;
        }

        if( !$newAdminGroupForm->isValid( $_POST ) )
        {
           $this->view->newAdminGroupForm = $newAdminGroupForm;
           return;
        }

        $newAdminGroupFormArray = $newAdminGroupForm->getValues();

        $openLDAP = new Application_Model_Ldap();
        $openLDAP->addAdminGroup( $newAdminGroupFormArray );
 
/*
        $groupsDataRep = new Application_Model_GroupsDataRepository();
        $groupsDataRep->createAdminGroupDirectory($newAdminGroupFormArray['groupName']);       

        unset($_POST);

        $this->_forward('new-admin-group-acl', 
                        'Acl',
                         null,
                         array('groupName' => $newAdminGroupFormArray['groupName']) 
               );
*/
    }

    public function searchAdminsAction()
    {
        $searchAdminsForm = new Application_Form_SearchAdmins();

        $this->view->searchAdminsForm = $searchAdminsForm;
    }

    public function searchAdminsResultsAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You do not have permission to access this page";
            return;
        }

        $searchAdminsForm = new Application_Form_SearchAdmins();

        if( !$searchAdminsForm->isValid( $_POST ) )
        {
            $this->_forward('search-admins');
        }
        
        $resultsForm = new Application_Form_SearchAdminsResults();
        $resultsForm->setFilter($searchAdminsForm->getValues());

        $this->view->resultsForm = $resultsForm;

    }

    public function searchAdminsResultsHandlerAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You do not have permission to access this page";
            return;
        }      

        if( isset( $_POST['delete'] ) && !isset( $_POST['edit']) )
        {
            $this->_forward('delete-admins');
        }

        if( isset( $_POST['edit'] ) && !isset( $_POST['delete']) )
        {
            $adminOldDn = $_POST['results'][0];  //the edit is applied only to the first selected group
            unset( $_POST );
            $this->_forward('edit-admin', 
                            'Administration', 
                             null,
                             array( 'adminOldDn' => $adminOldDn )
                   );
        }

    }

    public function deleteAdminsAction()
    {
        $ldap = new Application_Model_Ldap();

        foreach($_POST['results'] as $adminDn )
            $ldap->deleteEntry( $adminDn );

    }

    public function editAdminAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You do not have permission to access this page";
            return;
        }
       
        if( !isset($_POST)) 
        {     
            $reqParams = $this->getRequest()->getParams();       
            $adminOldDn = $reqParams['adminOldDn'];
            $editAdminForm = new Application_Form_EditAdmin(); 
            $editAdminForm->fill( $adminOldDn );
            $this->view->editAdminForm = $editAdminForm;

            return;
        }

        $editAdminForm = new Application_Form_EditAdmin();
 
        if( !$editAdminForm->isValid($_POST) )
        {
            $this->view->editAdminForm = $editAdminForm;
            return;
        }
      
        $ldap = new Application_Model_Ldap();
        $ldap->updateAdmin( $editAdminForm->getValues() );
 
    }

    public function searchAdminGroupsAction()
    {
        $searchAdminGroupsForm = new Application_Form_SearchAdminGroups();

        if( !$this->getRequest()->isPost() )
        {
           $this->view->searchAdminGroupsForm = $searchAdminGroupsForm;
           return;
        }

        if( !$searchAdminGroupsForm->isValid( $_POST ) )
        {
           $this->view->searchAdminGroupsForm = $searchAdminGroupsForm;
           return;
        }    
    }

    public function searchAdminGroupsResultsAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You do not have permission to access this page";
            return;
        }

        $searchAdminGroupsForm = new Application_Form_SearchAdminGroups();

        if( !$searchAdminGroupsForm->isValid( $_POST ) )
        {
            $this->_forward('search-admin-groups');
        }

        $resultsForm = new Application_Form_SearchAdminGroupsResults();
        $resultsForm->setFilter( $searchAdminGroupsForm->getValue('groupName') );

        $this->view->resultsForm = $resultsForm;
    }

    public function searchAdminGroupsResultsHandlerAction()
    {
        if( !$this->getRequest()->isPost() )
        {
            echo "You do not have permission to access this page";
            return;
        }      


        if( isset( $_POST['delete'] ) && !isset( $_POST['editInfo']) && !isset($_POST['editAcl']) ) 
        {
            $this->_forward('delete-admin-groups');
        }

        if( isset( $_POST['editInfo'] ) && !isset( $_POST['delete']) && !isset($_POST['editAcl']) )
        {
            $groupOldDn = $_POST['results'][0];  //the edit is applied only to the first selected group
            unset( $_POST );
            $this->_forward('edit-admin-group-info', 
                            'Administration', 
                             null,
                             array( 'groupOldDn' => $groupOldDn )
                   );
        }
/*
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
*/
    }

    public function editAdminGroupInfoAction()
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
            $editAdminGroupInfoForm = new Application_Form_EditAdminGroupInfo(); 
            $editAdminGroupInfoForm->fill( $groupOldDn );
            $this->view->editAdminGroupInfoForm = $editAdminGroupInfoForm;

            return;
        }

        $editAdminGroupInfoForm = new Application_Form_EditAdminGroupInfo();
 
        if( !$editAdminGroupInfoForm->isValid($_POST) )
        {
            $this->view->editAdminGroupInfoForm = $editAdminGroupInfoForm;
            return;
        }
      
        $ldap = new Application_Model_Ldap();
        $ldap->updateAdminGroup( $editAdminGroupInfoForm->getValues() );
    }

    public function deleteAdminGroupsAction()
    {
        $ldap = new Application_Model_Ldap();

        foreach($_POST['results'] as $groupDn )
        {
            $ldap->deleteEntry( $groupDn );
            
//            $groupName = substr($groupDn, 3, strpos($groupDn, ',')-3);
//            $groupsDataRep = new Application_Model_GroupsDataRepository();
//            $groupsDataRep->removeGroupDirectory($groupName);
        } 
    }

    public function importAdminsAction()
    {
        $importAdminsForm = new Application_Form_ImportAdmins();

        if(!$this->getRequest()->isPost())
        {
            $this->view->importAdminsForm = $importAdminsForm;
            return;
        }

        if(!$importAdminsForm->isValid($_POST))
        {
            $this->view->importAdminsForm = $importAdminsForm;
        }

        $ldap = new Application_Model_Ldap();
        $ldap->importUsersAsAdmins($importAdminsForm->getValues());
    }

    public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_helper->redirector('index','login');
        } 
    }


}

