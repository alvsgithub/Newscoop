<?php

namespace Newscoop\Entity\Proxy;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class NewscoopEntityArticlesProxy extends \Newscoop\Entity\Articles implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    private function _load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    
    public function setId($p_id)
    {
        $this->_load();
        return parent::setId($p_id);
    }

    public function getId()
    {
        $this->_load();
        return parent::getId();
    }

    public function getName()
    {
        $this->_load();
        return parent::getName();
    }

    public function setCommentsEnabled($p_enabled)
    {
        $this->_load();
        return parent::setCommentsEnabled($p_enabled);
    }

    public function setPublication(\Newscoop\Entity\Publications $p_publication)
    {
        $this->_load();
        return parent::setPublication($p_publication);
    }

    public function getPublication()
    {
        $this->_load();
        return parent::getPublication();
    }

    public function setLanguage(\Newscoop\Entity\Languages $p_language)
    {
        $this->_load();
        return parent::setLanguage($p_language);
    }

    public function getLanguage()
    {
        $this->_load();
        return parent::getLanguage();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'language', 'publication', 'comments_enabled', 'comments_locked', 'name');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}