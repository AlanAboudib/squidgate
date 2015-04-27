<?php

class Application_Form_EditGroupAcl extends Zend_Form
{

    public function init()
    {
        $this->setAction('/Acl/edit-group-acl')
             ->setMethod('post')
             ->setAttrib('id', 'group-acl');

        $this->addElement( 'textarea', 'deniedSites', array(
                 'label' => 'Denied Sites',
                 'attribs' => array('cols' => 48, 'rows' => 6),
                 'order' => 0
               ));

        $this->addElement( 'hidden', 'groupName');

        $this->addElement( 'submit', 'update', array(
                 'label' => 'Update',
                 'order' => 1
               ));
    }

    public function fill($groupName)
    {
        $groupRep = new Application_Model_GroupsDataRepository();
        $this->getElement('deniedSites')->setValue( $groupRep->getDeniedSites($groupName) );

        $this->getElement('groupName')->setValue($groupName);
    }


}

