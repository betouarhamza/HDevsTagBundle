<?php
/**
 * Created by Hamza Betouar
 * Email: betouar.hamza.89@gmail.com
 */
namespace HDevs\TagBundle\Manager;

class TagManager
{

    private $config;

    public function setConfig($config){
        $this->config = $config;
    }

    public function getConfig($key){
        return !isset($this->config[$key]) ? null :$this->config[$key];
    }
}