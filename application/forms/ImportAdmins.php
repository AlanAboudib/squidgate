<?php

class Application_Form_ImportAdmins extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Administration/import-admins')
             ->setMethod('post')
             ->setAttrib('id', 'import-admins');

        $ldap = new Application_Model_Ldap();

        $this->addElement( 'select', 'group', array(
                 'label' => 'Import into Group',
                 'multiOptions' => $ldap->getGroupNames('administrationGroups'),
                 'order' => 0
               ));

        $this->addElement( 'multiCheckbox', 'selectUsers', array(
                 'label' => 'Select Users to Import',
                 'multiOptions' => $ldap->getUserNames(),
                 'order' => 1
               ));

        $this->addElement( 'submit', 'import', array(
                 'label' => 'Import',
                 'order' => 2
               ));

    }



}

