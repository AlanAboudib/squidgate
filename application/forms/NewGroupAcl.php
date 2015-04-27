<?php

class Application_Form_NewGroupAcl extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Acl/new-group-acl')
             ->setMethod('post')
             ->setAttrib('id', 'group-acl');

        $this->addElement( 'textarea', 'deniedSites', array(
                 'label' => 'Denied Sites',
                 'attribs' => array('cols' => 48, 'rows' => 6),
                 'order' => 0
               ));

        $this->addElement( 'hidden', 'groupName');

        $this->addElement( 'submit', 'save', array(
                 'label' => 'Save',
                 'order' => 1
               ));
    }

    public function fill($groupName)
    {
        $this->getElement('groupName')->setValue($groupName);
    }

}

