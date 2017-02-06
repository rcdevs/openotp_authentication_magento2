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

namespace RCDevs\OpenOTP\Setup;
 
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
	protected $logger;

	public function __construct(
		\Psr\Log\LoggerInterface $loggerInterface
	){
		$this->logger = $loggerInterface;
		//$this->logger->debug('++++++++++++++++++++  Setup script _Construct OK  ++++++++++++++++++++ ');
	}
	
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $tableName = $setup->getTable('admin_user');
		//$this->logger->debug('++++++++++++++++++++  Version: '.$context->getVersion().' ++++++++++++++++++++++');

        if(!$context->getVersion()) {
			//$this->logger->debug('++++++++++++++++++++  Install script Launched  ++++++++++++++++++++++');
             //no previous version found, installation, InstallSchema was just executed
             //be careful, since everything below is true for installation !
	         if ($setup->getConnection()->isTableExists($tableName) == true) {
	         	$connection = $setup->getConnection();
	 			$connection->addColumn(
	 				$tableName,
	 			    'openotp_enable',
	 			    ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,'length' => 30,'nullable' => false, 'default' => null, 'afters' => 'password', 'comment' => 'Enable OpenOTP User']
	 			);
	       	 }	   			 
         }else{
			//$this->logger->debug('+++++++++++++++++++++++  Upgrade script  ++++++++++++++++++++++++');
			if ($setup->getConnection()->isTableExists($tableName) == true) {
				//$this->logger->debug('+++++++++++++++++++++++  La table ' . $tableName . ' exist  ++++++++++++++++++++++++');
				$connection = $setup->getConnection();
				$connection->addColumn(
					$tableName,
				    'openotp_enabled',
				    ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,'length' => 30,'nullable' => false, 'default' => '', 'after' => 'password', 'comment' => 'Enable OpenOTP User']
				);
			}
		}
	   
		// KEEP FOR NEXT UPGRADES: Exemple: if cur version is lower than à 1.3.0
        if (version_compare($context->getVersion(), '1.3.0', '<')) {
              if ($setup->getConnection()->isTableExists($tableName) == true) {
				//$this->logger->debug('+++++++++++++++++++++++  OpenOTP v'.$context->getVersion().' Installed, New 1.3.0 will be upgraded  ++++++++++++++++++++++++');
            }
        }

        $setup->endSetup();
    }
}