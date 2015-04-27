<?php

class Application_Form_SearchUsers extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Gateway/search-users-results')
             ->setMethod('post')
             ->setAttrib('id','search-users');

        $this->addElement('text', 'userId', array(
                 'label' => 'User ID',
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

