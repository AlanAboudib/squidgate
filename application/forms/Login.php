<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        $this->setAction('/login/process')
             ->setMethod('post')
             ->setAttrib('id', 'login');

        $this->addElement( 'text', 'adminId', array(
                 'required' => true,
                 'label' => 'Admin ID',
                 'order' => 0
               ));    
         $this->addElement( 'password', 'password', array(
                 'required' => true,
                 'label' => 'Password',
                 'order' => 1       
               ));

        $this->addElement( 'submit', 'login', array(
                 'label' => 'Login',
                 'order' => 2
               ));
   }


}

