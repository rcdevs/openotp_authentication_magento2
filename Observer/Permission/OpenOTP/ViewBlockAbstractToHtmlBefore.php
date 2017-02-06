<?php
/**
 * OpenOTP Authentication Magento 2 module
 *
 * LICENSE
 *
 * Copyright © 2017.
 * RCDevs OpenOTP. All rights reserved.
 *
 * The use and redistribution of this software, either compiled or uncompiled, with or without modifications are permitted provided that the following conditions are met:
 * *
 * @copyright Copyright (c) 2017 RCDevs (http://www.rcdevs.com)
 * @author rcdevs <info@rcdevs.com>
 * @category RCDevs
 * @package RCDevs_OpenOTP
 */

namespace RCDevs\OpenOTP\Observer\Permission\OpenOTP;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;


/**
 * Add New Tab in User Permission to enable OpenOTP per User 
 */
class ViewBlockAbstractToHtmlBefore extends WidgetTabs implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendAuthSession;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
		\Psr\Log\LoggerInterface $logger
    ) {
		parent::_construct();
        $this->request = $request;
        $this->backendAuthSession = $backendAuthSession;
		$this->_logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(EventObserver $observer)
    {
		$block = $observer->getEvent()->getBlock();
		
		if($block instanceof \Magento\User\Block\User\Edit\Tabs){
			$block->addTab(
				'openotp_section',
				[
					'label' => __('OpenOTP setup'),
					'title' => __('OpenOTP setup'),
					'after' => __('roles_section'),
	                'content'   => $block->getLayout()->createBlock('RCDevs\OpenOTP\Block\Adminhtml\Permission\User\Edit\Tab\Openotp')->toHtml(),
					'active'	=> true
				]
			);
		}

		return $this;

    }
}
