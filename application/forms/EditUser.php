<?php

class Application_Form_EditUser extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Gateway/edit-user')
             ->setMethod('post')
             ->setAttrib('id', 'edit-user');

        $userId = new Zend_Form_Element_Text('userId');
        $userId->setLabel( 'User ID*' )
               ->addValidator('alnum')
               ->setRequired(true)
               ->setOrder(0);

        $this->addElement( $userId );

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
                 'multiOptions' => $ldap->searchGroups('*'),
                 'order' => 8
               ));

        $this->addElement( 'hidden', 'userOldDn');

        $this->addElement( 'hidden', 'userOldPassword');
        
        $this->addElement( 'submit', 'update', array(
                 'label' => 'Update',
                 'order' => 9
               ));

    }

    public function fill($userDn)
    {
        $ldap = new Application_Model_Ldap();
        $userEntry = $ldap->getUserEntry($userDn);

        $this->getElement('userOldDn')->setValue($userDn);
        $this->getElement('userOldPassword')->setValue($userEntry['userpassword'][0]);
        $this->getElement('userId')->setValue($userEntry['uid'][0]);
        $this->getElement('firstName')->setValue($userEntry['givenname'][0]);
        $this->getElement('middleName')->setValue($userEntry['cn'][0]);
        $this->getElement('lastName')->setValue($userEntry['sn'][0]);

        if( isset($userEntry['telephonenumber'][0]) )
            $this->getElement('phoneNo')->setValue($userEntry['telephonenumber'][0]);

        if( isset($userEntry['mail'][0]) )
            $this->getElement('eMail')->setValue($userEntry['mail'][0]);

        $this->getElement('group')->setValue( $ldap->getUserGroupDn($userDn) );
    }


}

