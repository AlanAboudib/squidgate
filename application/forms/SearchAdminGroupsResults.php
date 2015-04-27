<?php

class Application_Form_SearchAdminGroupsResults extends Zend_Form
{

    public function init()
    {
       $this->setAction('/Administration/search-admin-groups-results-handler')
             ->setMethod('post')
             ->setAttrib('id', 'search-admin-groups-results');

       $this->addElement( 'multiCheckbox', 'results', array(
                 'order' => 0
               ));

       $this->addElement( 'submit', 'editInfo', array(
                 'label' => 'Edit Info',
                 'order' => 1
               ));

       $this->addElement( 'submit', 'editAcl', array(
                 'label' => 'Edit ACL',
                 'order' => 2
               ));
 
       $this->addElement( 'submit', 'delete', array(
                 'label' => 'Delete',
                 'order' => 3
               ));
    }

    public function setFilter($groupFilter)
    {
        $ldap = new Application_Model_Ldap();
        $this->getElement('results')->setMultiOptions($ldap->searchAdminGroups($groupFilter));
    }

}

