<?php

namespace SocialogAdmin\Authentication\Storage;

use Zend\Authentication\Storage;
use Zend\Authentication\Storage\StorageInterface;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcUser\Mapper\UserInterface as UserMapper;

class Db implements Storage\StorageInterface, ServiceManagerAwareInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var UserMapper
     */
    protected $mapper;

    /**
     * @var mixed
     */
    protected $resolvedIdentity;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Returns true if and only if storage is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->getStorage()->isEmpty();
    }

    /**
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @return mixed
     */
    public function read()
    {
        if (null !== $this->resolvedIdentity) {
            return $this->resolvedIdentity;
        }

        $identity = $this->getStorage()->read();

        if (is_int($identity) || is_scalar($identity)) {
            $identity = $this->getMapper()->findById($identity);
        }

        if ($identity) {
            $this->resolvedIdentity = $identity;
        } else {
            $this->resolvedIdentity = null;
        }

        return $this->resolvedIdentity;
    }

    /**
     * Writes $contents to storage
     *
     * @param  mixed $contents
     * @return void
     */
    public function write($contents)
    {
        $this->resolvedIdentity = null;
        $this->getStorage()->write($contents);
    }

    /**
     * Clears contents from storage
     *
     * @return void
     */
    public function clear()
    {
        $this->resolvedIdentity = null;
        $this->getStorage()->clear();
    }

    /**
     * getStorage
     *
     * @return Storage\StorageInterface
     */
    public function getStorage()
    {
        if (null === $this->storage) {
            $this->setStorage(new Storage\Session);
        }
        return $this->storage;
    }

    /**
     * setStorage
     *
     * @param Storage\StorageInterface $storage
     * @access public
     * @return Db
     */
    public function setStorage(Storage\StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * getMapper
     *
     * @return UserMapper
     */
    public function getMapper()
    {
        if (null === $this->mapper) {
            $this->mapper = $this->getServiceManager()->get('zfcuser_user_mapper');
        }
        return $this->mapper;
    }

    /**
     * setMapper
     *
     * @param UserMapper $mapper
     * @return Db
     */
    public function setMapper(UserMapper $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
}