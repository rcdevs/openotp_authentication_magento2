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
 * OpenOTP Auth model
 */
class Auth extends \Magento\Backend\Model\Auth
{
    /**
     * @var \RCDevs\OpenOTP\Model\Auth
     */
	public $openotpAuth = NULL;
    /**
     * @var \RCDevs\OpenOTP\Model\Auth
     */
    protected $_state = NULL;
    /**
     * @var \RCDevs\OpenOTP\Model\Auth
     */
    protected $_domain = NULL;
    /**
     * @var \RCDevs\OpenOTP\Model\Auth
     */
    protected $_username = NULL;
    /**
     * @var \RCDevs\OpenOTP\Model\Auth
     */
    protected $_password = NULL;
    /**
     * @var \RCDevs\OpenOTP\Model\Auth
     */
    protected $_u2f = NULL;
    /**
     * @var \RCDevs\OpenOTP\Model\Auth
     */
	protected $_userMagentoExist = false;
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddressInstance;	
    /**
     * @var \RCDevs\OpenOTP\Model\Auth
     * To deactivate OpenOTP Authentication 
     */
	protected $_disableOpenOTP	= false;
	
	
    
	/**
     * @var \RCDevs\OpenOTP\Model\Config
     */
    protected $_openOTPConfig;
    /**
     * @var \RCDevs\OpenOTP\Model\AuthFactory
     */
    protected $_openOTPAuthFactory;

	
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData;
    /**
     * @var \Magento\Backend\Model\Auth\StorageInterface
     */
    protected $_authStorage;	
    /**
     * @var \Magento\Backend\Model\Auth\Credential\StorageInterface
     */
    protected $_credentialStorage;	
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_coreConfig;	
    /**
     * @var \Magento\Framework\Data\Collection\ModelFactory
     */
    protected $_modelFactory;	
	

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_sessionManager;	
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */	
    protected $_moduleReader;
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;
	/**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrlInterface;
    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userUserFactory;	
    /**
     * @var \Magento\Authorization\Model\Role
     */
    protected $_userRole;	
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;


    public function __construct(
    	\RCDevs\OpenOTP\Model\Config $openOTPConfig,
		\RCDevs\OpenOTP\Model\OpenotpAuthFactory $openOTPAuthFactory,
	
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Backend\Helper\Data $backendData,
		\Magento\Backend\Model\Auth\StorageInterface $authStorage,
		\Magento\Backend\Model\Auth\Credential\StorageInterface $credentialStorage,
		\Magento\Framework\App\Config\ScopeConfigInterface $coreConfig,
		\Magento\Framework\Data\Collection\ModelFactory $modelFactory,
		
        \Magento\Framework\App\Request\Http $request,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
		\Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Stdlib\Cookie\CookieMetadata $cookieCookieMetadata,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Backend\Model\UrlInterface $backendUrlInterface,
        \Magento\User\Model\UserFactory $userUserFactory,
		\Magento\Authorization\Model\Role $userRole,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		
        \Psr\Log\LoggerInterface $logger 

	) {
        $this->_openOTPConfig = $openOTPConfig;
        $this->_openOTPAuthFactory = $openOTPAuthFactory;
		
		$this->_objectManager = $objectManager;
        $this->_eventManager = $eventManager;
        $this->_backendData = $backendData;
        $this->_authStorage = $authStorage;
        $this->_credentialStorage = $credentialStorage;
        $this->_coreConfig = $coreConfig;
        $this->_modelFactory = $modelFactory;
		
        $this->_request = $request;
        $this->_backendAuthSession = $backendAuthSession;
		$this->_sessionManager = $sessionManager;
		$this->_moduleReader = $moduleReader;
        $this->_cookieCookieMetadata = $cookieCookieMetadata;
	    $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;		
        $this->_backendUrlInterface = $backendUrlInterface;
        $this->_userUserFactory = $userUserFactory;
        $this->_userRole = $userRole;
        $this->_messageManager = $messageManager;
		
        $this->_remoteAddressInstance = $this->_objectManager->get(
             'Magento\Framework\HTTP\PhpEnvironment\RemoteAddress'
         );
        
		$this->logger = $logger;
 		//$this->logger->debug("******************* Class Model/Auth Loaded *******************");	

        parent::__construct(
	        $eventManager,
	        $backendData,
	        $authStorage,
	        $credentialStorage,
	        $coreConfig,
	        $modelFactory
		);
    }

    public function login($username, $password)
    {
		
        /** @var $session Mage_Admin_Model_Session */		
	    $session = $this->_backendAuthSession;	
        /* @var $config RCDevs_Openotp_Model_Config */
        $config = $this->_openOTPConfig;
        /* @var $openotpAuth RCDevs_Openotp_Model_OpenotpAuth */		
        $this->openotpAuth = $this->_openOTPAuthFactory->create();
			
		$etcModuleDir = $this->_moduleReader->getModuleDir('etc', 'RCDevs_OpenOTP'); 
		$this->openotpAuth->setEtcModuleDir($etcModuleDir);

		$request = $this->_request;					
		$remote_addr = $this->_remoteAddressInstance->getRemoteAddress();;		
			
		$userEnabled = 2;
		$session->setShowOpenOTPChallenge(false);	
		$session->setOpenOTPSuccess(false);			 		
	
		// Check OpenOTP WSDL file
		if (!$this->openotpAuth->checkFile('/openotp.wsdl','Could not load OpenOTP WSDL file')){
			$this->logger->error(__("Could not load OpenOTP module (WSDL file missing)"));
			self::throwException(__('Could not load OpenOTP module (WSDL file missing)'));
			return false;
		}
		// Check SOAP extension is loaded
		if (!$this->openotpAuth->checkSOAPext()){
			$this->logger->error(__("Your PHP installation is missing the SOAP extension"));
			self::throwException(__('Your PHP installation is missing the SOAP extension'));
			return false;
		}		
		
		// Get context cookie
		$context_name = $this->openotpAuth->getContext_name();
		$context_size = $this->openotpAuth->getContext_size();
		$context_time = $this->openotpAuth->getContext_time();
			
		$context = $this->_cookieManager->getCookie($context_name);

		if ($context) $this->_log("[OpenOTP Module] Context Cookie found: ".$context);
		
		// Get Backend redirect URL
        $backendUrl = $this->_backendUrlInterface;
		if ($backendUrl->useSecretKey())
			$backendUrl->renewSecretUrls();
        $path = $backendUrl->getStartupPageUrl();
        $redirectURL = $backendUrl->getUrl($path);
	
		
        if (empty($username)) {
			self::throwException(__('You did not sign in correctly or your account is temporarily disabled.'));			
            return false;
        }else{
			$this->_username = $username;
			$this->_password = $request->getPost('openotp_password') != NULL ? $request->getPost('openotp_password') : $password;
			$this->_u2f = $request->getPost('openotp_u2f') != NULL ? $request->getPost('openotp_u2f') : "";
			if( $this->_u2f != NULL ) $this->_password = NULL;
			$this->_state = $request->getPost('openotp_state');			
		}
		
        try {
			$this->load_Parameters($config);			
	
			$t_domain = $this->openotpAuth->getDomain($this->_username);
			if (is_array($t_domain)){
				$this->_username = $t_domain['username'];
				$this->_domain = $t_domain['domain'];
			}elseif($request->getPost('openotp_domain')!= NULL) $this->_domain = $request->getPost('openotp_domain');
			else $this->_domain = $t_domain;

			//User exists in Magento ?
		    //  @var \Magento\User\Model\User $user
		    $user = $this->_objectManager->get('Magento\User\Model\User')->loadByUsername($this->_username);
			
			if($user->getId()){
				$this->_userMagentoExist = true;
				$this->_log('[OpenOTP Module] User '.$this->_username.' exists in Magento');
				$userEnabled = $user->getOpenotp_enabled();
			}else $this->_log('[OpenOTP Module] User '.$this->_username.' does not exists in Magento');
			
			if($userEnabled == 1)
				$this->_log('[OpenOTP Module] User '.$this->_username.' has OpenOTP enabled setting to Yes');
			elseif($userEnabled == 2)
				$this->_log('[OpenOTP Module] User '.$this->_username.' has OpenOTP enabled setting to No');
			else $this->_log('[OpenOTP Module] User '.$this->_username.' has OpenOTP enabled setting to Default');
			
			
			// User enabled?
			$session->setIsUserEnabled($userEnabled);		

			//If deactivated do normal Auth
			if ( ( ( !$config->isEnabled() && $userEnabled != 1 ) || ( $config->isEnabled() && $userEnabled == 2 ) || $this->_disableOpenOTP )  && $this->_userMagentoExist ){
				$this->_log('User '.$this->_username.' tries login with Magento methods');
				return parent::login($this->_username, $this->_password, $request);
			}

			if ($this->_state != NULL) {
				// OpenOTP Challenge
				$resp = $this->openotpAuth->openOTPChallenge($this->_username, $this->_domain, $this->_state, $this->_password, $this->_u2f);				
			} else {
				// OpenOTP Login
				$resp = $this->openotpAuth->openOTPSimpleLogin($this->_username, $this->_domain, utf8_encode($this->_password), $remote_addr, $context);
			}
			// Debug OpenOTP Response
			$this->_log("[OpenOTP Module] OpenOTP SOAP Response:");
			$this->_log($resp);
			
			if (!$resp || !isset($resp['code'])) {
				$this->_log('Invalid OpenOTP response for user '.$this->_username);
				self::throwException(__('An error occurred while processing your request. Please contact administrator'));	
				return false;
			}

			switch ($resp['code']) {
				 case 0:
					if ($resp['message']) $msg = $resp['message'];
					else $msg = 'An error occurred while processing your request';
					
					self::throwException(__($msg));	
					break;
				 case 1:
					$this->_log("[OpenOTP Module] User ".$this->_username." successfully authenticate to OpenOTP Server ");
					$session->setShowOpenOTPChallenge(false);	
					$session->setOpenOTPSuccess(true);	

					try {
						// **********************  Create User Account  **********************
						if (!$this->_userMagentoExist){
							if(	$config->getCreateAccount()	){
								$user = $this->_userUserFactory->create()
									->setData(array(
										'username'  => $this->_username,
										'firstname'  => 'FirstName',
										'lastname'  => $this->_username,
										'email'  => $this->_username."@openotp.com",
										'password'  => $password."OpenOTP007",
										'openotp_enabled'  => 1,
										'is_active' => 1
									))->save();
								$this->_log("[OpenOTP Module] User succesfully created on Magento");
								$this->_messageManager->addSuccess( __('User succesfully created on Magento') );
								$user->setRoleId(array(1))
									->save();
							}
						}
						
						// **********************  Process Magento Login  **********************
					    $request->setPathInfo('/admin');
					    
						$session->setUser($user);
					    $session->processLogin();

					    if ($session->isLoggedIn()) {
					        $cookieValue = $session->getSessionId();
					        if ($cookieValue) {
								if(class_exists('Magento\Security\Model\AdminSessionsManager')){
					                $adminSessionManager = $this->_objectManager->get('Magento\Security\Model\AdminSessionsManager'); 
					                $adminSessionManager->processLogin(); 
								}
					        }
							
							// **********************  Create context cookie  **********************
							if (extension_loaded('openssl')) {			
								$this->_log("[OpenOTP Module] Openssl extension is loaded ");
								if (strlen($context) != $context_size) $context = bin2hex(openssl_random_pseudo_bytes($context_size/2));
								$duration = time()+$context_time;
							
								$this->_log("[OpenOTP Module] Create Context Cookie ");
								$this->_log("[OpenOTP Module] Cookie Name: " . $context_name);
								$this->_log("[OpenOTP Module] Cookie Value: " . $context);
								$this->_log("[OpenOTP Module] Cookie Duration: " . $duration);
							
								$metadata = $this->_cookieMetadataFactory
						            ->createPublicCookieMetadata()
						            ->setDuration($duration)
						            ->setPath("/")
						            ->setDomain(NULL)
								//***********************************************************************************************
								//******************************* TODO: COOKIE SSL ONLY OR NOT **********************************
								//***********************************************************************************************
			                        ->setSecure(false)
			                        ->setHttpOnly(true);								

						        $this->_cookieManager->setPublicCookie(
						            $context_name,
						            $context,
						            $metadata
									); 
								
								$this->_log("[OpenOTP Module] Context Cookie successfully created");
							} else	$this->_log("[OpenOTP Module] Openssl extension is not loaded: Impossible to create Context Cookie, trusted Authentication not available(see Docs).");							
							
							$this->_eventManager->dispatch('admin_session_user_login_success', array('user' => $user));
							$this->_log("[OpenOTP Module] User " .$this->_username. " successfully authenticate to Magento2 Admin Portal");
							$this->_log("[OpenOTP Module] URL to Redirect: " . $redirectURL);
					        header('Location:  '.$redirectURL);						
							return;
						}
						
					} catch (\Exception $e) {
							$this->error($e->getMessage());
							return false;
					}
					break;
				 case 2:
					$session->setShowOpenOTPChallenge(true);
					$js = $this->openotpAuth->getOverlay( $resp['otpChallenge'], $resp['u2fChallenge'], $resp['message'], $this->_username, $resp['session'], $resp['timeout'], $this->_password, $this->_domain);
					$session->setOpenotpFrontendScript($js);		
					
					$this->_log("[OpenOTP Module] Challenge required ");
					header('Location:  '.$redirectURL);	
							
					return false;	
					break;
				 default:
					$session->setShowOpenOTPChallenge(false);			 				 
					$this->_log('Invalid OpenOTP response for user '.$this->_username, JLog::ERROR, $remote_addr);
					self::throwException(__('An error occurred while processing your request. Please contact administrator'));	
					break;
			}
			
        }catch (\Exception $e) {
            $this->_eventManager->dispatch('admin_session_user_login_failed',
				array('user_name' => $username, 'exception' => $e));
            if ($request && !$request->getParam('messageSent')) {
				$this->_messageManager->addError( __("DiVA".$e->getMessage()) );
                $request->setParam('messageSent', true);
            }
        }

        return $user;
    }
	
    private function load_Parameters($config){
        $this->openotpAuth->setServer_url($config->getServerUrl());
        $this->openotpAuth->setClient_id($config->getClientId());
		$this->openotpAuth->setDefault_domain($config->getDefaultDomain());
        $this->openotpAuth->setClient_settings($config->getClientSettings());
        $this->openotpAuth->setProxy_host($config->getProxyHost());
        $this->openotpAuth->setProxy_port($config->getProxyPort());
        $this->openotpAuth->setProxy_login($config->getProxyLogin());
        $this->openotpAuth->setProxy_password($config->getProxyPassword());
        $this->openotpAuth->setProxy_password($config->isLogEnabled());
    }

    public function _log($mess)
    {
        if(is_array($mess) || is_object($mess)){
            $mess = print_r($mess, true);
        }
		($this->_openOTPConfig->isLogEnabled() == 1) ? $this->logger->debug($mess) : "";	
    }

    public function _error($message, $type="core") {
		
		self::throwException($message);			
		return false;
    }
}
