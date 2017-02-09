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

namespace RCDevs\OpenOTP\Helper;

/**
 * openOTP service class
 */

class SoapClientTimeout extends \SoapClient
{
    private $timeout;
    private $version;

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }
    public function setVersion($version)
    {
        $this->version = $version;
    }
    
    public function __doRequest($request, $location, $action, $version, $one_way = false)
    {
        if (!$this->timeout) {
            // Call via parent because we require no timeout
            $response = parent::__doRequest($request, $location, $action, $version, $one_way);
        } else {
            // Call via Curl and use the timeout
            $curl = curl_init($location);

            curl_setopt($curl, CURLOPT_VERBOSE, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: text/xml", "API-Version: ".strval($this->version)]);
            curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);

            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                throw new \Exception(curl_error($curl));
            }
            curl_close($curl);
        }
        if (!$one_way) {
            return ($response);
        }
    }
}
