<?php

class Application_Form_EditAdmin extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Administration/edit-admin')
             ->setMethod('post')
             ->setAttrib('id', 'edit-admin');

        $adminId = new Zend_Form_Element_Text('adminId');
        $adminId->setLabel( 'Admin ID*' )
               ->addValidator('alnum')
               ->setRequired(true)
               ->setOrder(0);

        $this->addElement( $adminId );

        $this->addElement( 'text', 'firstName', array(
                 'validators' => array( 'Alnum'),
                 'required' => true,
                 'label' => 'First Name*',
                 'order' => 1
               ));    
       
        $this->addElement( 'text', 'middleName', array(
                 'validators' => array( 'Alnum'),
                 'required' => true,
                 'label' => 'Middle Name*',
                 'order' => 2
               ));
       
        $this->addElement( 'text', 'lastName', array(
                 'validators' => array( 'Alnum'),
                 'required' => true,
                 'label' => 'Last Name*',
                 'order' => 3
               ));
        
        $this->addElement( 'text', 'phoneNo', array(
                 'label' => 'Phone Number',
                 'order' => 4
               ));

        $this->addElement( 'text', 'eMail', array(
                 'label' => 'E-Mail',
                 'validators' => array( 'EmailAddress'),
                 'filters' => array( 'StringToLower' ),
                 'order' => 5
               ));

        $this->addElement( 'password', 'password', array(
                 'label' => 'New Password',
                 'order' => 6
               ));

        $this->addElement( 'password', 'rePassword', array(
                 'label' => 'Re-Password',
                 'order' => 7
               ));

        $ldap = new Application_Model_Ldap();
        $this->addElement( 'select', 'group', array(
                 'label' => 'Group',
                 'multiOptions' => $ldap->getGroupNames('administrationGroups'),
                 'order' => 8
               ));

        $this->addElement( 'hidden', 'adminOldDn');

        $this->addElement( 'hidden', 'adminOldPassword');
        
        $this->addElement( 'submit', 'update', array(
                 'label' => 'Update',
                 'order' => 9
               ));

    }

    public function fill($adminDn)
    {
        $ldap = new Application_Model_Ldap();
        $adminEntry = $ldap->getAdminEntry($adminDn);

        $this->getElement('adminOldDn')->setValue($adminDn);
        $this->getElement('adminOldPassword')->setValue($adminEntry['userpassword'][0]); //userpassword cannot be changed to adminpassword because it is an ldap-specific attribute
        $this->getElement('adminId')->setValue($adminEntry['uid'][0]);
        $this->getElement('firstName')->setValue($adminEntry['givenname'][0]);
        $this->getElement('middleName')->setValue($adminEntry['cn'][0]);
        $this->getElement('lastName')->setValue($adminEntry['sn'][0]);

        if( isset($adminEntry['telephonenumber'][0]) )
            $this->getElement('phoneNo')->setValue($adminEntry['telephonenumber'][0]);

        if( isset($adminEntry['mail'][0]) )
            $this->getElement('eMail')->setValue($adminEntry['mail'][0]);

        $this->getElement('group')->setValue( $ldap->getAdminGroupDn($adminDn) );
    }

}

