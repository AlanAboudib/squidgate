<?php

class Application_Model_GroupsDataRepository
{

    public  $REP_PATH;    

    public function __construct()
    {
        $this->REP_PATH = "/srv/www/squidgate.local/squid/acl";
    }

    public function openDeniedSitesFile($groupName)
    {
        return fopen($this->getDeniedSitesFilePath($groupName), 'a+' );
    }

    public function getDeniedSites($groupName)
    {
        if(!$this->existsDeniedSitesFile($groupName))
            return null;

        else
        {
            $deniedSites = file( $this->getDeniedSitesFilePath($groupName), 
                                 FILE_IGNORE_NEW_LINES);
            $deniedSites = implode(', ', $deniedSites);
        }

        return $deniedSites;
                  
    }

    public function existsDeniedSitesFile($groupName)
    {
        if(file_exists($this->getDeniedSitesFilePath($groupName)))
            return true;
        else
            return false;
    }

    public function getDeniedSitesFilePath($groupName)
    {
        return $this->REP_PATH.'/gatewayGroups/'.$groupName.'/DeniedSites';
    }
    
    public function createGroupDirectory($groupName)
    {
        mkdir($this->getGroupDirectoryPath($groupName));
        mkdir($this->getGroupDirectoryPath($groupName).'/config');
        
    }

    public function removeGroupDirectory($groupName)
    {
        $this->rrmdir($this->getGroupDirectoryPath($groupName));
    }

    public function getGroupDirectoryPath($groupName)
    {
        return $this->REP_PATH.'/gatewayGroups/'.$groupName;
    }


    public function rrmdir($dir)
    {
        foreach(glob($dir.'/*') as $file)
        {
            if(is_dir($file))
                $this->rrmdir($file);
            else
                unlink($file);
        }
        rmdir($dir);
    }
}

