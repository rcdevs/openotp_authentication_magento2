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

namespace RCDevs\OpenOTP\Block\Adminhtml\Permission\User\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Psr\Log\LoggerInterface;
/**
 * Additional tab for user permission configurartion
 */
class Openotp extends Generic
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    public function __construct(
		LoggerInterface $logger,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
		$this->logger = $logger;
        parent::__construct(
            $context,
			$registry,
			$formFactory,
            $data
        );
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->registry->registry('permissions_user');

        $form = $this->formFactory->create(
		 	['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
		);
        $form->setHtmlIdPrefix('user_');

        $fieldset = $form->addFieldset('openotp_fieldset', array('legend' => __('Enable OpenOTP Two factors authentication for login')));		
		$fieldset->addField('openotp', 'select', array(
			  'label'       => __('Enable OpenOTP'),
			  'name'      => 'openotp_enabled',
			  'value'  => $model->getOpenotp_enabled(),
			  'values' => array('-1'=>__('Default...'),'1' => 'Yes','2' => 'No'),
			  'disabled' => false,
			  'after_element_html' => '<div style="background-position:8px 11px; padding:5px 0 5px 36px; margin-top: 3px;" class="notification-global notification-global-notice">Override [Enable OpenOTP] Plugin setting in System / Configuration</div>',			  
			));
		
		$this->setForm($form);

        return parent::_prepareForm();
    }
}
