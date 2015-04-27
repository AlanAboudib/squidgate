<?php

class Application_Form_NewAdminGroup extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Administration/new-admin-group')
             ->setMethod('post')
             ->setAttrib('id', 'new-admin-group');

        $this->addElement( 'text', 'groupName', array(
                 'required' => true,
                 'validators' => array( 'Alnum' ),
                 'label' => 'Group Name*',
                 'order' => 0
               ));
 

        $ldap = new Application_Model_Ldap();

        $this->addElement( 'multiCheckbox', 'addAdmins', array(
                 'label' => 'Add Admins',
                 'order' => 1,
                 'MultiOptions' => $ldap->getAdminNames() 
               ));

        $this->addElement( 'submit', 'save', array(
                 'label' => 'Save',
                 'order' => 2
               ));
    }


}

