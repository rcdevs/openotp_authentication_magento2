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

namespace RCDevs\OpenOTP\Block\Adminhtml;

/**
 * OpenOTP login Admin Block
 */
class Adminlogin extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $openOTPSession;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
	
	
    public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Backend\Model\Auth\Session $openOTPSession,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->openOTPSession = $openOTPSession;
		//$this->logger->debug("******************* Class Block/Adminlogin Loaded *******************");
        parent::__construct($context);
    }
	
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	
    public function getFrontendScript()
    {
		$js = null;
	 	if ($this->openOTPSession->getShowOpenOTPChallenge() != NULL)
		{ 
			$js = $this->openOTPSession->getOpenotpFrontendScript(); 
			$this->openOTPSession->setShowOpenOTPChallenge(false); 
			$this->openOTPSession->setOpenotpFrontendScript(false); 
		}
        return $js;
    }
	
}