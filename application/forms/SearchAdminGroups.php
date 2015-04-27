<?php

class Application_Form_SearchAdminGroups extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Administration/search-admin-groups-results')
             ->setMethod('post')
             ->setAttrib('id', 'search-admin-groups');

        $this->addElement( 'text', 'groupName', array(
                 'label' => 'Group Name',
                 'order' => 0
               ));

        $this->addElement( 'submit', 'search', array(
                 'label' => 'Search',
                 'order' => 1
               ));
    }


}

