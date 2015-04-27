<?php

class Application_Form_SearchAdminsResults extends Zend_Form
{

    public function init()
    {
       $this->setAction('/Administration/search-admins-results-handler')
             ->setMethod('post')
             ->setAttrib('id', 'search-admins-results');

       $this->addElement( 'multiCheckbox', 'results', array(
                 'order' => 0
               ));
       $this->addElement( 'submit', 'edit', array(
                 'label' => 'Edit',
                 'order' => 1
               ));
       $this->addElement( 'submit', 'delete', array(
                 'label' => 'Delete',
                 'order' => 2
               ));
    }

    public function setFilter(array $filter)
    {
       $ldap = new Application_Model_Ldap();
       $results = $this->getElement('results');
       $results->setMultiOptions( $ldap->getAdminNames($filter) );
    }

}

