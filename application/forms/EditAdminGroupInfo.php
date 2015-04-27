<?php

class Application_Form_EditAdminGroupInfo extends Zend_Form
{

    public function init()
    {
         $this->setAction('/Administration/edit-admin-group-info')
             ->setMethod('post')
             ->setAttrib('id', 'edit-admin-group-info');

        $this->addElement( 'text', 'groupName', array(
                 'required' => true,
                 'validators' => array( 'Alnum' ),
                 'label' => 'Group Name*',
                 'order' => 0
               ));

        $ldap = new Application_Model_Ldap();
        $this->addElement( 'multiCheckbox', 'addAdmins', array(
                 'label' => 'Add Admins',
                 'multiOptions' => $ldap->getAdminNames(),
                 'order' => 1
               ));

        $this->addElement( 'hidden', 'groupOldDn');

        $this->addElement( 'submit', 'update', array(
                 'label' => 'Update',
                 'order' => 2
               ));
    }

    public function fill( $groupDn )
    {
        
        $groupCn = substr( $groupDn, 3, strpos( $groupDn, ',') - 3 );
        $this->getElement( 'groupName' )->setValue( $groupCn );

        $this->getElement( 'groupOldDn' )->setValue( $groupDn );

        $ldap = new Application_Model_Ldap();
        $this->getElement( 'addAdmins' )->setValue( $ldap->getAdminGroupMembers( $groupDn ) );
    }

}

