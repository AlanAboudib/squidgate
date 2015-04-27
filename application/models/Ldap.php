<?php

class Application_Model_Ldap 
{
    private $ldap; 
    
    public function __construct()
    {
       
        $options = array(
                           'host'              => 'squidgate.local',
                           'username'          => 'CN=Manager,DC=squidgate,DC=local',
                           'password'          => 'squid',
                           'bindRequiresDn'    =>  true,
                           'baseDn'            => 'DC=squidgate,DC=local'
                   );

        $this->ldap = new Zend_Ldap($options);
        $this->ldap->bind();
       

    }

    public function addUser( array $userForm ) //it is not necessary to write 'array' explicitly in php
    {
        $userDn = $this->getUserDn($userForm['userId']);
        $entry = array();

        Zend_Ldap_Attribute::setAttribute($entry, 'uid', $userForm['userId']);
        Zend_Ldap_Attribute::setAttribute($entry, 'givenName', $userForm['firstName']);
        Zend_Ldap_Attribute::setAttribute($entry, 'cn', $userForm['middleName']);
        Zend_Ldap_Attribute::setAttribute($entry, 'sn', $userForm['lastName']);
        Zend_Ldap_Attribute::setAttribute($entry, 'mail', $userForm['eMail']);
        Zend_Ldap_Attribute::setAttribute($entry, 'telephoneNumber', $userForm['phoneNo']);
        Zend_Ldap_Attribute::setAttribute($entry, 'userPassword', $userForm['password']);
        Zend_Ldap_Attribute::setAttribute($entry, 'objectClass', 'person');
        Zend_Ldap_Attribute::setAttribute($entry, 'objectClass', 'inetOrgPerson',true); 
       
        try //move the try-catch formula to the action controller not here
        {  
            $this->ldap->add($userDn, $entry);
        }
        catch(Exception $e)
        {
            echo '<br />'.$e->getMessage().'<br />'.$this->ldap->getLastErrorCode().'<br />'.$this->ldap->getLastError();
        }
        
        $groupDn = $userForm['group'];
        $this->addUserToGroup($userDn, $groupDn);
    }

    public function addAdmin(array $newAdminForm)
    {
        $adminDn = $this->getAdminDn($newAdminForm['adminId']);
        $entry = array();

        Zend_Ldap_Attribute::setAttribute($entry, 'uid', $newAdminForm['adminId']);
        Zend_Ldap_Attribute::setAttribute($entry, 'givenName', $newAdminForm['firstName']);
        Zend_Ldap_Attribute::setAttribute($entry, 'cn', $newAdminForm['middleName']);
        Zend_Ldap_Attribute::setAttribute($entry, 'sn', $newAdminForm['lastName']);
        Zend_Ldap_Attribute::setAttribute($entry, 'mail', $newAdminForm['eMail']);
        Zend_Ldap_Attribute::setAttribute($entry, 'telephoneNumber', $newAdminForm['phoneNo']);
        Zend_Ldap_Attribute::setAttribute($entry, 'userPassword', $newAdminForm['password']);
        Zend_Ldap_Attribute::setAttribute($entry, 'objectClass', 'person');
        Zend_Ldap_Attribute::setAttribute($entry, 'objectClass', 'inetOrgPerson',true); 
       
        try //move the try-catch formula to the action controller not here
        {  
            $this->ldap->add($adminDn, $entry);
        }
        catch(Exception $e)
        {
            echo '<br />'.$e->getMessage().'<br />'.$this->ldap->getLastErrorCode().'<br />'.$this->ldap->getLastError();
        }
        
        $groupDn = $newAdminForm['group'];
        $this->addAdminToGroup($adminDn, $groupDn);

    }

    public function updateUser( $editUserForm )
    {
        //deleting user's old entry
        $userOldGroupDn = $this->getUserGroupDn($editUserForm['userOldDn']);

        $this->removeUserFromGroup($editUserForm['userOldDn'],
                                   $userOldGroupDn
               );

        $this->deleteEntry( $editUserForm['userOldDn'] );
       

        //adding user's new entry
        $userDn = $this->getUserDn($editUserForm['userId']);
        $userEntry = array();

        Zend_Ldap_Attribute::setAttribute($userEntry, 'uid', $editUserForm['userId']);
        Zend_Ldap_Attribute::setAttribute($userEntry, 'givenName', $editUserForm['firstName']);
        Zend_Ldap_Attribute::setAttribute($userEntry, 'cn', $editUserForm['middleName']);
        Zend_Ldap_Attribute::setAttribute($userEntry, 'sn', $editUserForm['lastName']);
        Zend_Ldap_Attribute::setAttribute($userEntry, 'mail', $editUserForm['eMail']);
        Zend_Ldap_Attribute::setAttribute($userEntry, 'telephoneNumber', $editUserForm['phoneNo']);

        $newPassword = $editUserForm['userOldPassword'];

        if( $editUserForm['password'] != "" )
            $newPassword = $editUserForm['password'];
            
        Zend_Ldap_Attribute::setAttribute($userEntry, 'userPassword', $newPassword);
        Zend_Ldap_Attribute::setAttribute($userEntry, 'objectClass', 'person');
        Zend_Ldap_Attribute::setAttribute($userEntry, 'objectClass', 'inetOrgPerson',true); 

        try //move the try-catch formula to the action controller not here
        {  
            $this->ldap->add($userDn, $userEntry);
        }
        catch(Exception $e)
        {
            echo '<br />'.$e->getMessage().'<br />'.$this->ldap->getLastErrorCode().'<br />'.$this->ldap->getLastError();
        }
 

        $this->addUserToGroup( $userDn, $editUserForm['group'] );

    }

    public function updateAdmin( $editAdminForm )
    {
        //deleting user's old entry
        $adminOldGroupDn = $this->getAdminGroupDn($editAdminForm['adminOldDn']);

        $this->removeAdminFromGroup($editAdminForm['adminOldDn'],
                                   $adminOldGroupDn
               );

        $this->deleteEntry( $editAdminForm['adminOldDn'] );
       

        //adding user's new entry
        $adminDn = $this->getAdminDn($editAdminForm['adminId']);
        $adminEntry = array();

        Zend_Ldap_Attribute::setAttribute($adminEntry, 'uid', $editAdminForm['adminId']);
        Zend_Ldap_Attribute::setAttribute($adminEntry, 'givenName', $editAdminForm['firstName']);
        Zend_Ldap_Attribute::setAttribute($adminEntry, 'cn', $editAdminForm['middleName']);
        Zend_Ldap_Attribute::setAttribute($adminEntry, 'sn', $editAdminForm['lastName']);
        Zend_Ldap_Attribute::setAttribute($adminEntry, 'mail', $editAdminForm['eMail']);
        Zend_Ldap_Attribute::setAttribute($adminEntry, 'telephoneNumber', $editAdminForm['phoneNo']);

        $newPassword = $editAdminForm['adminOldPassword'];

        if( $editAdminForm['password'] != "" )
            $newPassword = $editAdminForm['password'];
            
        Zend_Ldap_Attribute::setAttribute($adminEntry, 'userPassword', $newPassword);
        Zend_Ldap_Attribute::setAttribute($adminEntry, 'objectClass', 'person');
        Zend_Ldap_Attribute::setAttribute($adminEntry, 'objectClass', 'inetOrgPerson',true); 

        try //move the try-catch formula to the action controller not here
        {  
            $this->ldap->add($adminDn, $adminEntry);
        }
        catch(Exception $e)
        {
            echo '<br />'.$e->getMessage().'<br />'.$this->ldap->getLastErrorCode().'<br />'.$this->ldap->getLastError();
        }
 

        $this->addAdminToGroup( $adminDn, $editAdminForm['group'] );

    }


    public function addGroup( array $newGroupForm )
    {
        $entry = array();

        Zend_Ldap_Attribute::setAttribute($entry, 'cn', $newGroupForm['groupName']);
        
        foreach( $newGroupForm['addUsers'] as $userDn )
            Zend_Ldap_Attribute::setAttribute($entry, 
                                              'uniqueMember', 
                                              $userDn, 
                                               true
                                 );
       
        Zend_Ldap_Attribute::setAttribute($entry, 'objectClass', 'groupOfUniqueNames');
        

        try //move the try-catch formula to the action controller not here
        {  
            $this->ldap->add('cn='.$newGroupForm['groupName'].',ou=gatewayGroups,dc=squidgate,dc=local', $entry);
        }
        catch(Exception $e)
        {
            echo '<br />'.$e->getMessage().'<br />'.$this->ldap->getLastErrorCode().'<br />'.$this->ldap->getLastError();
        }
      
    }

    public function addAdminGroup( array $newAdminGroupForm )
    {
        $entry = array();

        Zend_Ldap_Attribute::setAttribute($entry, 'cn', $newAdminGroupForm['groupName']);
        
        foreach( $newAdminGroupForm['addAdmins'] as $adminDn )
            Zend_Ldap_Attribute::setAttribute($entry, 
                                              'uniqueMember', 
                                              $adminDn, 
                                               true
                                 );
       
        Zend_Ldap_Attribute::setAttribute($entry, 'objectClass', 'groupOfUniqueNames');
        

        try //move the try-catch formula to the action controller not here
        {  
            $this->ldap->add('cn='.$newAdminGroupForm['groupName'].',ou=administrationGroups,dc=squidgate,dc=local', $entry);
        }
        catch(Exception $e)
        {
            echo '<br />'.$e->getMessage().'<br />'.$this->ldap->getLastErrorCode().'<br />'.$this->ldap->getLastError();
        }

    }

    public function updateGroup( $editGroupForm )
    {
        $this->deleteEntry( $editGroupForm['groupOldDn'] );
        $this->addGroup($editGroupForm);
    }

    public function updateAdminGroup( $editAdminGroupInfoForm )
    {
        $this->deleteEntry( $editAdminGroupInfoForm['groupOldDn'] );
        $this->addAdminGroup($editAdminGroupInfoForm);
    }

    public function searchGroups($groupName)
    {
        if($groupName == NULL || $groupName == '*')
            $groupName="*";
        else
            $groupName= '*'.$groupName.'*';

        $result = $this->ldap->search('(cn='.$groupName.')',
                                      'ou=gatewayGroups,dc=squidgate,dc=local',
                                       Zend_Ldap::SEARCH_SCOPE_ONE,
                                       array('cn')
                               );

        foreach ($result as $item)
            $groupList[ $item['dn'] ] = $item['cn'][0];
 
        return $groupList;
    }

    public function searchAdminGroups($groupName)
    {
        if($groupName == NULL || $groupName == '*')
            $groupName="*";
        else
            $groupName= '*'.$groupName.'*';

        $result = $this->ldap->search('(cn='.$groupName.')',
                                      'ou=administrationGroups,dc=squidgate,dc=local',
                                       Zend_Ldap::SEARCH_SCOPE_ONE,
                                       array('cn')
                               );

        foreach ($result as $item)
            $groupList[ $item['dn'] ] = $item['cn'][0];
 
        return $groupList;
    }

    public function getUserEntry($userDn)
    {
        return $this->ldap->getEntry($userDn);
    }

    public function getAdminEntry($adminDn)
    {
        return $this->ldap->getEntry($adminDn);
    }


    public function getUserNames( array $filter = NULL )
    {
        $filterString = '';

        if( is_array( $filter ) ) 
        {
            if( isset($filter['userId']) && $filter['userId'] != NULL )
                $filterString .= '(uid=*'.$filter['userId'].'*)';

            if( isset($filter['firstName']) && $filter['firstName'] != NULL )
                $filterString .= '(givenName=*'.$filter['firstName'].'*)';

            if( isset($filter['middleName']) && $filter['middleName'] != NULL ) 
                $filterString .= '(cn=*'.$filter['middleName'].'*)';

            if( isset($filter['lastName']) && $filter['lastName'] != NULL )
                $filterString .= '(sn=*'.$filter['lastName'].'*)';
            
            $filterString = '(&'.$filterString.')'; // it works even if we have only one search criteria
 
        }

        if( $filter == NULL ) 
            $filterString = '(objectClass=*)';
 
        $result = $this->ldap->search( $filterString,
                                      'ou=users,dc=squidgate,dc=local',
                                       Zend_Ldap::SEARCH_SCOPE_ONE
                               );
    
        $userNames = array();
        
        foreach ($result as $item) {
             $userNames[ $item["dn"] ] =  implode(" ", array( $item["givenname"][0], 
                                                                  $item["cn"][0], 
                                                                  $item["sn"][0] 
                                              ));

        }

        return $userNames;
   
    }

    public function getAdminNames( array $filter = NULL )
    {
        $filterString = '';

        if( is_array( $filter ) ) 
        {
            if( isset($filter['adminId']) && $filter['adminId'] != NULL )
                $filterString .= '(uid=*'.$filter['adminId'].'*)';

            if( isset($filter['firstName']) && $filter['firstName'] != NULL )
                $filterString .= '(givenName=*'.$filter['firstName'].'*)';

            if( isset($filter['middleName']) && $filter['middleName'] != NULL ) 
                $filterString .= '(cn=*'.$filter['middleName'].'*)';

            if( isset($filter['lastName']) && $filter['lastName'] != NULL )
                $filterString .= '(sn=*'.$filter['lastName'].'*)';
            
            $filterString = '(&'.$filterString.')'; // it works even if we have only one search criteria
 
        }

        if( $filter == NULL ) 
            $filterString = '(objectClass=*)';
 
        $result = $this->ldap->search( $filterString,
                                      'ou=admins,dc=squidgate,dc=local',
                                       Zend_Ldap::SEARCH_SCOPE_ONE
                               );
    
        $adminNames = array();
        
        foreach ($result as $item) {
             $adminNames[ $item["dn"] ] =  implode(" ", array( $item["givenname"][0], 
                                                                  $item["cn"][0], 
                                                                  $item["sn"][0] 
                                              ));

        }

        return $adminNames;
   
    }
 
    public function getGroupNames( $groupType )
    {
        $result = $this->ldap->search('(objectclass=*)',
                                      'ou='.$groupType.',dc=squidgate,dc=local',
                                       Zend_Ldap::SEARCH_SCOPE_ONE
                               );
    
        $groupNames = array();
        foreach ($result as $item) {
             $groupNames[ $item["dn"] ] =  $item["cn"][0]; 

        }

        return $groupNames;
   
    }

    public function deleteEntry( $Dn )
    {
        $this->ldap->delete( $Dn );
    }

    public function getUserGroupDn($userDn)
    {
        $groupDn = $this->ldap->search('(uniquemember='.$userDn.')',
                                      'ou=gatewayGroups,dc=squidgate,dc=local',
                                       Zend_Ldap::SEARCH_SCOPE_ONE,
                                       array('')
                               );

        $groupDn = $groupDn->toArray();

        return $groupDn[0]['dn'];
    }

    public function getAdminGroupDn($adminDn)
    {
        $groupDn = $this->ldap->search('(uniquemember='.$adminDn.')',
                                      'ou=administrationGroups,dc=squidgate,dc=local',
                                       Zend_Ldap::SEARCH_SCOPE_ONE,
                                       array('')
                               );

        $groupDn = $groupDn->toArray();

        return $groupDn[0]['dn'];
    }


    public function getGroupMembers( $groupDn )
    {
        //add code to make sure the groupDn is a group dn not some other dn

        $groupMembers = $this->ldap->getEntry( $groupDn, array('uniqueMember') ); //getEntry returns always the dn along with the specified attributes
       
        return $groupMembers["uniquemember"];
       
    }

    public function getAdminGroupMembers( $groupDn )
    {
        //add code to make sure the groupDn is a group dn not some other dn

        $groupMembers = $this->ldap->getEntry( $groupDn, array('uniqueMember') ); //getEntry returns always the dn along with the specified attributes
       
        return $groupMembers["uniquemember"];
       
    }

   
    public function removeUserFromGroup( $userDn, $groupDn )
    {
        $entry = $this->ldap->getEntry($groupDn);
        Zend_Ldap_Attribute::removeFromAttribute($entry, 'uniqueMember', $userDn);
        $this->ldap->update($groupDn, $entry);
    }

    public function removeAdminFromGroup( $adminDn, $groupDn )
    {
        $entry = $this->ldap->getEntry($groupDn);
        Zend_Ldap_Attribute::removeFromAttribute($entry, 'uniqueMember', $adminDn);
        $this->ldap->update($groupDn, $entry);
    }


    public function addUserToGroup($userDn, $groupDn)
    {
        $entry = $this->getGroupEntry($groupDn);
        Zend_Ldap_Attribute::setAttribute($entry, 'uniqueMember', $userDn, true);
        $this->ldap->update( $groupDn, $entry);
    }

    public function addAdminToGroup($adminDn, $groupDn)
    {
        $entry = $this->getGroupEntry($groupDn);
        Zend_Ldap_Attribute::setAttribute($entry, 'uniqueMember', $adminDn, true);
        $this->ldap->update( $groupDn, $entry);
    }

    public function importUsersAsAdmins(array $importAdminsForm)
    {
        foreach($importAdminsForm['selectUsers'] as $userDn)
        {
            $userEntry = $this->getUserEntry($userDn);
            $adminInfo = array();var_dump($userEntry);
            $adminInfo['adminId'] = $userEntry['uid'][0];
            $adminInfo['firstName'] = $userEntry['givenname'][0];
            $adminInfo['middleName'] = $userEntry['cn'][0];
            $adminInfo['lastName'] = $userEntry['sn'][0];
            $adminInfo['eMail'] = isset($userEntry['mail'][0])? $userEntry['mail'][0]: '';
            $adminInfo['phoneNo'] = isset($userEntry['telephonenumber'][0])? $userEntry['telephonenumber'][0]: '';
            $adminInfo['password'] = $userEntry['userpassword'][0];
            $adminInfo['group'] = $importAdminsForm['group'];

            $this->addAdmin($adminInfo);
        }
    }

    public function getAdminName($adminDn)
    {
        $adminEntry = $this->getAdminEntry($adminDn);
        $adminName = $adminEntry['givenname'][0]." ".$adminEntry['cn'][0]." ".$adminEntry['sn'][0];

        return $adminName;
    }   

    public function getUserName($userDn)
    {
        $userEntry = $this->getUserEntry($userDn);
        $userName = $userEntry['givenname'][0]." ".$userEntry['cn'][0]." ".$userEntry['sn'][0];

        return $userName;
    }   

 
    public function getGroupEntry( $groupDn )
    {
        return $this->ldap->getEntry( $groupDn );
    }

    public function getGroupDn( $groupName, $groupType )
    {
        $groupDn = 'cn='.$groupName.',ou='.$groupType.',dc=squidgate,dc=local'; 
        
        return $groupDn;
    }

    public function getUserDn($userId)
    {
        $userDn = 'uid='.$userId.',ou=users,dc=squidgate,dc=local';
 
        return $userDn;
    }

    public function getAdminDn($adminId)
    {
        $adminDn = 'uid='.$adminId.',ou=admins,dc=squidgate,dc=local';
 
        return $adminDn;
    }


}
