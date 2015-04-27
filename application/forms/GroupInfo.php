<?php

class Application_Form_GroupInfo extends Zend_Form
{

    public function init()
    {
       
    }

    public function newGroup()
    {
        $this->setAction('/Gateway/newgroup')
             ->setMethod('post')
             ->setAttrib('id', 'group-info');

        $this->addElement( 'text', 'groupName', array(
                 'required' => true,
                 'validators' => array( 'Alnum' ),
                 'label' => 'Group Name*',
                 'order' => 0
               ));
 

        $ldap = new Application_Model_Ldap();

        $this->addElement( 'multiCheckbox', 'addUsers', array(
                 'label' => 'Add Users',
                 'order' => 1,
                 'MultiOptions' => $ldap->getUserNames() 
               ));

        $this->addElement( 'submit', 'save', array(
                 'label' => 'Save',
                 'order' => 2
               ));
 
    }

    public function editGroup()
    {
        $this->setAction('/Gateway/edit-group-info')
             ->setMethod('post')
             ->setAttrib('id', 'edit-group-info');

        $this->addElement( 'text', 'groupName', array(
                 'required' => true,
                 'validators' => array( 'Alnum' ),
                 'label' => 'Group Name*',
                 'order' => 0
               ));

        $ldap = new Application_Model_Ldap();
        $this->addElement( 'multiCheckbox', 'addUsers', array(
                 'label' => 'AddUsers',
                 'multiOptions' => $ldap->getUserNames(),
                 'order' => 1
               ));

        $this->addElement( 'hidden', 'groupOldDn');

        $this->addElement( 'submit', 'update', array(
                 'label' => 'Update',
                 'order' => 2
               ));
 
    }

    public function populatedEditGroup( $groupDn )
    {
        $this->editGroup();
        
        $groupCn = substr( $groupDn, 3, strpos( $groupDn, ',') - 3 );
        $this->getElement( 'groupName' )->setValue( $groupCn );

        $this->getElement( 'groupOldDn' )->setValue( $groupDn );

        $ldap = new Application_Model_Ldap();
        $this->getElement( 'addUsers' )->setValue( $ldap->getGroupMembers( $groupDn ) );
    }
}

