<?php
/**
 * OpenOTP Authentication Magento 2 module
 *
 * LICENSE
 *
 * Copyright © 2017
 * RCDevs OpenOTP. All rights reserved.
 *
 * The use and redistribution of this software, either compiled or uncompiled, with or without modifications are permitted provided that the following conditions are met:
 * *
 * @copyright Copyright (c) 2017 RCDevs (http://www.rcdevs.com)
 * @author rcdevs <info@rcdevs.com>
 * @category RCDevs
 * @package RCDevs_OpenOTP
 */

namespace RCDevs\OpenOTP\Model;

/**
 * Abstraction for store config to fetch global openotp settings
 */
class Config extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var string
     */
    const XML_PATH_OPENOTP_ENABLED = 'rcdevs_openotp/main/enabled';

    /**
     * @var string
     */
    const XML_PATH_OPENOTP_SERVER_URL = 'rcdevs_openotp/main/server_url';

    /**
     * @var string
     */
    const XML_PATH_OPENOTP_CLIENT_ID = 'rcdevs_openotp/main/client_id';
    
    /**
     * @var string
     */
    const XML_PATH_OPENOTP_CREATE_ACCOUNT = 'rcdevs_openotp/main/create_account';

    /**
     * @var string
     */
    const XML_PATH_OPENOTP_DEFAULT_DOMAIN = 'rcdevs_openotp/main/default_domain';

    /**
     * @var string
     */
    const XML_PATH_OPENOTP_CLIENT_SETTINGS = 'rcdevs_openotp/main/client_settings';

    /**
     * @var string
     */
    const XML_PATH_OPENOTP_PROXY_HOST = 'rcdevs_openotp/main/proxy_host';

    /**
     * @var string
     */
    const XML_PATH_OPENOTP_PROXY_PORT = 'rcdevs_openotp/main/proxy_port';

    /**
     * @var string
     */
    const XML_PATH_OPENOTP_PROXY_LOGIN = 'rcdevs_openotp/main/proxy_login';

    /**
     * @var string
     */
    const XML_PATH_OPENOTP_PROXY_PASSWORD = 'rcdevs_openotp/main/proxy_password';
    
    /**
     * @var string
     */
    const XML_PATH_OPENOTP_LOG_ENABLED = 'rcdevs_openotp/main/log_enabled';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $logger;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->logger = $this->_objectManager->get('\Psr\Log\LoggerInterface');
        $this->scopeConfig = $scopeConfig;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }



    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPENOTP_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 1;
    }

    /**
     * @return string
     */
    public function getServerUrl()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPENOTP_SERVER_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPENOTP_CLIENT_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getCreateAccount()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPENOTP_CREATE_ACCOUNT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 1;
    }

    /**
     * @return string
     */
    public function getDefaultDomain()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPENOTP_DEFAULT_DOMAIN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getClientSettings()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPENOTP_CLIENT_SETTINGS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getProxyHost()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPENOTP_PROXY_HOST, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getProxyPort()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPENOTP_PROXY_PORT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getProxyLogin()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPENOTP_PROXY_LOGIN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getProxyPassword()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPENOTP_PROXY_PASSWORD, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isLogEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPENOTP_LOG_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 1;
    }
}
