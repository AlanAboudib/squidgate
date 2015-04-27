<?php

class Application_Model_Squid
{
    protected $status = 0;
    public $SQUID_CONFIG_DIR = '/srv/www/squidgate.local/squid/config';
    public $SQUID_DIR = '/srv/www/squidgate.local/squid';

    public function proxyStart()
    {
        system("sudo squid -f /srv/www/squidgate.local/squid/config/squid.conf", &$status);
    }

    public function proxyStop()
    { 
        system("sudo squid -k shutdown", &$status);
    }

    public function proxyReconfigure()
    {
        system("sudo squid -f /srv/www/squidgate.local/squid/config/squid.conf -k reconfigure", &$status);
    }
    
    public function isOn()
    {
        system("sudo squid -k check", &$status );
        
        if( $status == 0 )
	    return 1;
        else
            return 0;
    }

    public function groupAclManager( array $groupAclForm )
    {
        $this->deniedSitesManager( $groupAclForm['groupName'], $groupAclForm['deniedSites'] );
        $this->makeGroupConfigFile( $groupAclForm['groupName'] );
        $this->makeGlobalConfigFile();
    }

    public function deniedSitesManager( $groupName, $deniedSites )
    {
        $sitesList = explode(',', $deniedSites);

        $groupsRep = new Application_Model_GroupsDataRepository();
        
        if($groupsRep->existsDeniedSitesFile($groupName))
            unlink($groupsRep->getDeniedSitesFilePath($groupName));

        $deniedSitesFile = $groupsRep->openDeniedSitesFile($groupName);

        foreach($sitesList as $site)
        {
            fwrite($deniedSitesFile, rtrim(ltrim($site))."\n" );
                      
        }

        fclose($deniedSitesFile);
    }
    
    public function makeGroupConfigFile($groupName)
    {
        $groupsRep = new Application_Model_GroupsDataRepository();
        $groupDirPath = $groupsRep->getGroupDirectoryPath($groupName);
        $groupConfigFile = fopen($groupDirPath.'/config/'.$groupName.'_Acl.conf','w+');

        $config = 'acl '.$groupName.'DeniedSites dstdomain \''.$groupsRep->getDeniedSitesFilePath($groupName).'\''."\n";
        $config .= 'acl '.strtolower($groupName).' external ldap_gatewayGroup '.$groupName."\n";
        $config .= 'http_access deny '.strtolower($groupName).' '.$groupName.'DeniedSites'."\n";

        fwrite($groupConfigFile, $config);
        fclose($groupConfigFile);
    }

    public function makeGlobalConfigFile()
    {
        if(file_exists($this->SQUID_CONFIG_DIR.'/squid.conf'))
            unlink($this->SQUID_CONFIG_DIR.'/squid.conf');

        $squidConfigFile = fopen($this->SQUID_CONFIG_DIR.'/squid.conf','w+');    
        fwrite($squidConfigFile, 'include '.$this->SQUID_CONFIG_DIR.'/squidDefault_top.conf'."\n");

        $ldap = new Application_Model_Ldap();
        $groupNames = $ldap->getGroupNames('gatewayGroups');

        foreach($groupNames as $dn => $groupName)
            fwrite($squidConfigFile, 'include '.$this->SQUID_DIR.'/acl/gatewayGroups/'.$groupName.'/config/*.conf'."\n");

        fwrite($squidConfigFile, 'include '.$this->SQUID_CONFIG_DIR.'/squidDefault_bottom.conf'."\n");

        fclose($squidConfigFile);
    }
}

