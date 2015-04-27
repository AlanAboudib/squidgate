<?php

class Application_Form_SearchAdmins extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Administration/search-admins-results')
             ->setMethod('post')
             ->setAttrib('id','search-admins');

        $this->addElement('text', 'adminId', array(
                 'label' => 'Admin ID',
                 'order' => 0
               ));

        $this->addElement('text', 'firstName', array(
                 'label' => 'First Name',
                 'order' => 1
               ));

        $this->addElement('text', 'middleName', array(
                 'label' => 'Middel Name',
                 'order' => 2
               ));

        $this->addElement('text', 'lastName', array(
                 'label' => 'Last Name',
                 'order' => 3
               ));

        $this->addElement('submit', 'search', array(
                 'label' => 'Search',
                 'order' => 4
               ));
    }


}

