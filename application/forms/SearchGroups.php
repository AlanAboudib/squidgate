<?php

class Application_Form_SearchGroups extends Zend_Form
{

    public function init()
    { 
    }

    public function search()
    {
        $this->setAction('/Gateway/searchgroupsresults')
             ->setMethod('post')
             ->setAttrib('id', 'search-groups');

        $this->addElement( 'text', 'groupName', array(
                 'label' => 'Group Name',
                 'order' => 0
               ));

        $this->addElement( 'submit', 'search', array(
                 'label' => 'Search',
                 'order' => 1
               ));
    }

    public function results( $groupFilter )
    {
       $this->setAction('/Gateway/search-Groups-Results-Handler')
             ->setMethod('post')
             ->setAttrib('id', 'search-groups-results');

       $ldap = new Application_Model_Ldap();
       $this->addElement( 'multiCheckbox', 'results', array(
                 'multiOptions' => $ldap->searchGroups( $groupFilter ),
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
}

