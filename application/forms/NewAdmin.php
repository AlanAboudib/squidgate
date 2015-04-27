<?php

class Application_Form_NewAdmin extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Administration/new-admin')
             ->setMethod('post')
             ->setAttrib('id', 'new-admin');

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
                 'required' => true,
                 'label' => 'Password*',
                 'order' => 6       
               ));

        $this->addElement( 'password', 'rePassword', array(
                 'required' => true,
                 'label' => 'Re-Password*',
                 'order' => 7
               ));

        $ldap = new Application_Model_Ldap();
        $this->addElement( 'select', 'group', array(
                 'label' => 'Group',
                 'order' => 8,
                 'multiOptions' => $ldap->getGroupNames('administrationGroups')
               ));

        $this->addElement( 'submit', 'save', array(
                 'label' => 'Save',
                 'order' => 9
               ));

  }


}

