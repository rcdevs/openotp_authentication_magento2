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
 * openOTP service class
 */
class OpenotpAuth extends \Zend_Service_Abstract
{

    protected $etcModuleDir;
    protected $server_url;
    protected $client_id;
    protected $default_domain;
    protected $client_settings;
    protected $proxy_host;
    protected $proxy_port;
    protected $proxy_username;
    protected $proxy_password;
    protected $soap_client;
    protected $context_name = 'OpenOTPContext';
    protected $context_size = 32;
    protected $context_time = 2500000;
    protected $soapClientTimeoutFactory;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $HelperBackend;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Backend\Helper\Data $HelperBackend,
        \RCDevs\OpenOTP\Helper\SoapClientTimeoutFactory $SoapClientTimeoutFactory
    ) {
        $this->soapClientTimeoutFactory = $SoapClientTimeoutFactory;
        $this->helper = $HelperBackend;
        $this->logger = $logger;
        $this->logger->debug("******************* Class Model/OpenotpAuth Loaded *******************");
    }
    /**
     * Check if File exists
     *
     * @param string $file
     * @return bool
     */
    public function checkFile($file)
    {
        if (!file_exists($this->etcModuleDir . '/'.$file)) {
            return false;
        }
        return true;
    }
    
    /**
     * Check if SOAP extension loaded
     *
     * @return bool
     */
    public function checkSOAPext()
    {
        if (!extension_loaded('soap')) {
            return false;
        }
        return true;
    }

        
    public function getDomain($username)
    {
        $pos = strpos($username, "\\");
        if ($pos) {
            $ret['domain'] = substr($username, 0, $pos);
            $ret['username'] = substr($username, $pos+1);
        } else {
            $ret = $this->default_domain;
        }
        return $ret;
    }
    
    public function getOverlay($otpChallenge, $u2fChallenge, $message, $username, $session, $timeout, $ldappw, $domain)
    {
        $backendurl = $this->helper->getHomePageUrl();
        $overlay = <<<EOT
		function addOpenOTPDivs(){
			var overlay_bg = document.createElement("div");
			overlay_bg.id = 'openotp_overlay_bg';
			overlay_bg.style.position = 'fixed'; 
			overlay_bg.style.top = '0'; 
			overlay_bg.style.left = '0'; 
			overlay_bg.style.width = '100%'; 
			overlay_bg.style.height = '100%'; 
			overlay_bg.style.background = 'grey';
			overlay_bg.style.zIndex = "9998"; 
			overlay_bg.style["filter"] = "0.9";
			overlay_bg.style["-moz-opacity"] = "0.9";
			overlay_bg.style["-khtml-opacity"] = "0.9";
			overlay_bg.style["opacity"] = "0.9";
		
			var overlay = document.createElement("div");
			overlay.id = 'openotp_overlay';
			overlay.style.position = 'absolute'; 
			overlay.style.top = '165px'; 
			overlay.style.left = '50%'; 
			overlay.style.width = '280px'; 
			overlay.style.marginLeft = '-180px';
			overlay.style.padding = '65px 40px 50px 40px';
			overlay.style.background = 'url('+path+'openotp_banner.png) no-repeat top left #E4E4E4';
			overlay.style.border = '5px solid #545454';
			overlay.style.borderRadius = '10px';
			overlay.style.MozBorderRadius = '10px';
			overlay.style.WebkitBorderRadius = '10px';
			overlay.style.boxShadow = '1px 1px 12px #555555';
			overlay.style.WebkitBoxShadow = '1px 1px 12px #555555';
			overlay.style.MozBoxShadow = '1px 1px 12px #555555';
			overlay.style.zIndex = "9999"; 
			overlay.style.boxSizing= "initial";
			overlay.innerHTML = '<a style="position:absolute; top:-12px; right:-12px;" href="$backendurl" title="close"><img src="'+path+'openotp_closebtn.png"/></a>'
			+ '<style>'
			+ 'blink { -webkit-animation: blink 1s steps(5, start) infinite; -moz-animation:    blink 1s steps(5, start) infinite; -o-animation:      blink 1s steps(5, start) infinite; animation: blink 1s steps(5, start) infinite; }'
			+ '	@-webkit-keyframes blink { to { visibility: hidden; } }'
			+ '@-moz-keyframes blink { to { visibility: hidden; } }'
			+ '@-o-keyframes blink { to { visibility: hidden; } }'
			+ '@keyframes blink { to { visibility: hidden; } }'
			+ '</style>'				
			+ '<div style="background-color:red; margin:0 -40px 0; height:4px; width:360px; padding:0;" id="count_red"><div style="background-color:orange; margin:0; height:4px; width:360px; padding:0;" id="div_orange"></div></div>'
			+ '<form id="loginForm" autocomplete="off" style="margin-top:30px; display:block;" action="" method="POST">'
			+ '<input type="hidden" name="form_key" value="'+token+'">'
            + '<input type="hidden" id="username" name="login[username]" value="$username">'
            + '<input type="hidden" id="login" name="login[password]" class="required-entry input-text" value="$ldappw" />'			
			+ '<input type="hidden" name="openotp_state" value="$session">'
			+ '<input type="hidden" name="openotp_domain" value="$domain">'
			+ '<table width="100%">'
			+ '<tr><td style="text-align:center; font-weight:bold; font-size:14px;">$message</td></tr>'
			+ '<tr><td id="timout_cell" style="text-align:center; padding-top:4px; font-weight:bold; font-style:italic; font-size:11px;">Timeout: <span id="timeout">$timeout seconds</span></td></tr>'
EOT;
        if ($otpChallenge || ( !$otpChallenge && !$u2fChallenge )) {
            $overlay .=<<<EOT
			+ '<tr><td id="inputs_cell" style="text-align:center; padding-top:25px;"><input class="required-entry input-text"  type="password" size="15" id="openotp_password" name="openotp_password">&nbsp;'
			+ '<input style="padding:3px 10px;" type="submit" value="Ok" class="form-button"></td></tr>'
EOT;
        }
            
        if ($u2fChallenge) {
            $overlay .= "+ '<tr style=\"border:none; background:none\"><td id=\"inputs_cell\" style=\"text-align:center; padding-top:5px; border:none;\"><input type=\"hidden\" name=\"openotp_u2f\" value=\"\">'";
            if ($otpChallenge) {
                $overlay .= "+ '<b>U2F response</b> &nbsp; <blink id=\"u2f_activate\">[Activate Device]</blink></td></tr>'";
            } else {
                $overlay .= "+ '<img src=\"'+path+'/u2f.png\"><br><br><blink id=\"u2f_activate\">[Activate Device]</blink></td></tr>'";
            }
        }
                        
            $overlay .=<<<EOT
			+ '</table></form>';
			
			document.body.appendChild(overlay_bg);    
			document.body.appendChild(overlay); 
		}
		
		addOpenOTPDivs();
		
		/* Compute Timeout */	
		var c = $timeout;
		var base = $timeout;
		function count()
		{
			plural = c <= 1 ? "" : "s";
			document.getElementById("timeout").innerHTML = c + " second" + plural;
			var div_width = 360;
			var new_width =  Math.round(c*div_width/base);
			document.getElementById('div_orange').style.width=new_width+'px';
			
			if( document.getElementById('openotp_password') ){ 
				document.getElementById('openotp_password').focus();
			}
			
			if(c == 0 || c < 0) {
				c = 0;
				clearInterval(timer);
				document.getElementById("timout_cell").innerHTML = " <b style='color:red;'>Login timedout!</b> ";
				document.getElementById("inputs_cell").innerHTML = "<input style='padding:3px 20px;' type='button' value='Retry' class='button mainaction' onclick='window.location.href=\"$_SERVER[PHP_SELF]\"'>";
			}
			c--;
		}
		count();
		
		
		function getInternetExplorerVersion() {
		
			var rv = -1;
		
			if (navigator.appName == "Microsoft Internet Explorer") {
				var ua = navigator.userAgent;
				var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
				if (re.exec(ua) != null)
					rv = parseFloat(RegExp.$1);
			}
			return rv;
		}
		
		var ver = getInternetExplorerVersion();
		
		if (navigator.appName == "Microsoft Internet Explorer"){
			if (ver <= 10){
				toggleItem = function(){
					
				    var el = document.getElementsByTagName("blink")[0];
				    if (el.style.display === "block") {
				        el.style.display = "none";
				    } else {
				        el.style.display = "block";
				    }
				}
				var t = setInterval(function() {toggleItem; }, 1000);
			}
		}
		
		var timer = setInterval(function() {count();  }, 1000);
EOT;
        
        //Copyright 2014-2015 Google Inc. All rights reserved.	//Use of this source code is governed by a BSD-style //license that can be found in the LICENSE file or at	//https://developers.google.com/open-source/licenses/bsd
    	$overlay.= '"use strict";var u2f=u2f||{},js_api_version;u2f.EXTENSION_ID="kmendfapggjehodndflmmgagdbamhnfd",u2f.MessageTypes={U2F_REGISTER_REQUEST:"u2f_register_request",U2F_REGISTER_RESPONSE:"u2f_register_response",U2F_SIGN_REQUEST:"u2f_sign_request",U2F_SIGN_RESPONSE:"u2f_sign_response",U2F_GET_API_VERSION_REQUEST:"u2f_get_api_version_request",U2F_GET_API_VERSION_RESPONSE:"u2f_get_api_version_response"},u2f.ErrorCodes={OK:0,OTHER_ERROR:1,BAD_REQUEST:2,CONFIGURATION_UNSUPPORTED:3,DEVICE_INELIGIBLE:4,TIMEOUT:5},u2f.U2fRequest,u2f.U2fResponse,u2f.Error,u2f.Transport,u2f.Transports,u2f.SignRequest,u2f.SignResponse,u2f.RegisterRequest,u2f.RegisterResponse,u2f.RegisteredKey,u2f.GetJsApiVersionResponse,u2f.getMessagePort=function(a){if("undefined"!=typeof chrome&&chrome.runtime){var b={type:u2f.MessageTypes.U2F_SIGN_REQUEST,signRequests:[]};chrome.runtime.sendMessage(u2f.EXTENSION_ID,b,function(){chrome.runtime.lastError?u2f.getIframePort_(a):u2f.getChromeRuntimePort_(a)})}else u2f.isAndroidChrome_()?u2f.getAuthenticatorPort_(a):u2f.isIosChrome_()?u2f.getIosPort_(a):u2f.getIframePort_(a)},u2f.isAndroidChrome_=function(){var a=navigator.userAgent;return a.indexOf("Chrome")!=-1&&a.indexOf("Android")!=-1},u2f.isIosChrome_=function(){return $.inArray(navigator.platform,["iPhone","iPad","iPod"])>-1},u2f.getChromeRuntimePort_=function(a){var b=chrome.runtime.connect(u2f.EXTENSION_ID,{includeTlsChannelId:!0});setTimeout(function(){a(new u2f.WrappedChromeRuntimePort_(b))},0)},u2f.getAuthenticatorPort_=function(a){setTimeout(function(){a(new u2f.WrappedAuthenticatorPort_)},0)},u2f.getIosPort_=function(a){setTimeout(function(){a(new u2f.WrappedIosPort_)},0)},u2f.WrappedChromeRuntimePort_=function(a){this.port_=a},u2f.formatSignRequest_=function(a,b,c,d,e){if(void 0===js_api_version||js_api_version<1.1){for(var f=[],g=0;g<c.length;g++)f[g]={version:c[g].version,challenge:b,keyHandle:c[g].keyHandle,appId:a};return{type:u2f.MessageTypes.U2F_SIGN_REQUEST,signRequests:f,timeoutSeconds:d,requestId:e}}return{type:u2f.MessageTypes.U2F_SIGN_REQUEST,appId:a,challenge:b,registeredKeys:c,timeoutSeconds:d,requestId:e}},u2f.formatRegisterRequest_=function(a,b,c,d,e){if(void 0===js_api_version||js_api_version<1.1){for(var f=0;f<c.length;f++)c[f].appId=a;for(var g=[],f=0;f<b.length;f++)g[f]={version:b[f].version,challenge:c[0],keyHandle:b[f].keyHandle,appId:a};return{type:u2f.MessageTypes.U2F_REGISTER_REQUEST,signRequests:g,registerRequests:c,timeoutSeconds:d,requestId:e}}return{type:u2f.MessageTypes.U2F_REGISTER_REQUEST,appId:a,registerRequests:c,registeredKeys:b,timeoutSeconds:d,requestId:e}},u2f.WrappedChromeRuntimePort_.prototype.postMessage=function(a){this.port_.postMessage(a)},u2f.WrappedChromeRuntimePort_.prototype.addEventListener=function(a,b){var c=a.toLowerCase();"message"==c||"onmessage"==c?this.port_.onMessage.addListener(function(a){b({data:a})}):console.error("WrappedChromeRuntimePort only supports onMessage")},u2f.WrappedAuthenticatorPort_=function(){this.requestId_=-1,this.requestObject_=null},u2f.WrappedAuthenticatorPort_.prototype.postMessage=function(a){var b=u2f.WrappedAuthenticatorPort_.INTENT_URL_BASE_+";S.request="+encodeURIComponent(JSON.stringify(a))+";end";document.location=b},u2f.WrappedAuthenticatorPort_.prototype.getPortType=function(){return"WrappedAuthenticatorPort_"},u2f.WrappedAuthenticatorPort_.prototype.addEventListener=function(a,b){var c=a.toLowerCase();if("message"==c){var d=this;window.addEventListener("message",d.onRequestUpdate_.bind(d,b),!1)}else console.error("WrappedAuthenticatorPort only supports message")},u2f.WrappedAuthenticatorPort_.prototype.onRequestUpdate_=function(a,b){var c=JSON.parse(b.data),f=(c.intentURL,c.errorCode,null);c.hasOwnProperty("data")&&(f=JSON.parse(c.data)),a({data:f})},u2f.WrappedAuthenticatorPort_.INTENT_URL_BASE_="intent:#Intent;action=com.google.android.apps.authenticator.AUTHENTICATE",u2f.WrappedIosPort_=function(){},u2f.WrappedIosPort_.prototype.postMessage=function(a){var b=JSON.stringify(a),c="u2f://auth?"+encodeURI(b);location.replace(c)},u2f.WrappedIosPort_.prototype.getPortType=function(){return"WrappedIosPort_"},u2f.WrappedIosPort_.prototype.addEventListener=function(a,b){var c=a.toLowerCase();"message"!==c&&console.error("WrappedIosPort only supports message")},u2f.getIframePort_=function(a){var b="chrome-extension://"+u2f.EXTENSION_ID,c=document.createElement("iframe");c.src=b+"/u2f-comms.html",c.setAttribute("style","display:none"),document.body.appendChild(c);var d=new MessageChannel,e=function(b){"ready"==b.data?(d.port1.removeEventListener("message",e),a(d.port1)):console.error("First event on iframe port was not ready")};d.port1.addEventListener("message",e),d.port1.start(),c.addEventListener("load",function(){c.contentWindow.postMessage("init",b,[d.port2])})},u2f.EXTENSION_TIMEOUT_SEC=30,u2f.port_=null,u2f.waitingForPort_=[],u2f.reqCounter_=0,u2f.callbackMap_={},u2f.getPortSingleton_=function(a){u2f.port_?a(u2f.port_):(0==u2f.waitingForPort_.length&&u2f.getMessagePort(function(a){for(u2f.port_=a,u2f.port_.addEventListener("message",u2f.responseHandler_);u2f.waitingForPort_.length;)u2f.waitingForPort_.shift()(u2f.port_)}),u2f.waitingForPort_.push(a))},u2f.responseHandler_=function(a){var b=a.data,c=b.requestId;if(!c||!u2f.callbackMap_[c])return void console.error("Unknown or missing requestId in response.");var d=u2f.callbackMap_[c];delete u2f.callbackMap_[c],d(b.responseData)},u2f.sign=function(a,b,c,d,e){void 0===js_api_version?u2f.getApiVersion(function(f){js_api_version=void 0===f.js_api_version?0:f.js_api_version,console.log("Extension JS API Version: ",js_api_version),u2f.sendSignRequest(a,b,c,d,e)}):u2f.sendSignRequest(a,b,c,d,e)},u2f.sendSignRequest=function(a,b,c,d,e){u2f.getPortSingleton_(function(f){var g=++u2f.reqCounter_;u2f.callbackMap_[g]=d;var h="undefined"!=typeof e?e:u2f.EXTENSION_TIMEOUT_SEC,i=u2f.formatSignRequest_(a,b,c,h,g);f.postMessage(i)})},u2f.register=function(a,b,c,d,e){void 0===js_api_version?u2f.getApiVersion(function(f){js_api_version=void 0===f.js_api_version?0:f.js_api_version,console.log("Extension JS API Version: ",js_api_version),u2f.sendRegisterRequest(a,b,c,d,e)}):u2f.sendRegisterRequest(a,b,c,d,e)},u2f.sendRegisterRequest=function(a,b,c,d,e){u2f.getPortSingleton_(function(f){var g=++u2f.reqCounter_;u2f.callbackMap_[g]=d;var h="undefined"!=typeof e?e:u2f.EXTENSION_TIMEOUT_SEC,i=u2f.formatRegisterRequest_(a,c,b,h,g);f.postMessage(i)})},u2f.getApiVersion=function(a,b){u2f.getPortSingleton_(function(c){if(c.getPortType){var d;switch(c.getPortType()){case"WrappedIosPort_":case"WrappedAuthenticatorPort_":d=1.1;break;default:d=0}return void a({js_api_version:d})}var e=++u2f.reqCounter_;u2f.callbackMap_[e]=a;var f={type:u2f.MessageTypes.U2F_GET_API_VERSION_REQUEST,timeoutSeconds:"undefined"!=typeof b?b:u2f.EXTENSION_TIMEOUT_SEC,requestId:e};c.postMessage(f)})};';

        if ($u2fChallenge) {
            $overlay .= "if (/chrome|chromium|firefox|opera/.test(navigator.userAgent.toLowerCase())) {
			    var u2f_request = ".$u2fChallenge.";
			    var u2f_regkeys = [];
			    for (var i=0, len=u2f_request.keyHandles.length; i<len; i++) {
			        u2f_regkeys.push({version:u2f_request.version,keyHandle:u2f_request.keyHandles[i]});
			    }
			    u2f.sign(u2f_request.appId, u2f_request.challenge, u2f_regkeys, function(response) {
					document.getElementsByName('openotp_u2f')[0].value = JSON.stringify(response); 
					document.getElementById('loginForm').submit();					
			    }, $timeout ); }" . "\r\n";
            $overlay .= " else { 
				var u2f_activate = document.getElementById('u2f_activate'); 
				u2f_activate.innerHTML = '[Not Supported]'; 
				u2f_activate.style.color='red'; 
				}" . "\r\n";
        }

        /*
		if( $u2fChallenge ) $overlay .= " 
			if (/chrom(e|ium)/.test(navigator.userAgent.toLowerCase())) {
				u2f.sign([".$u2fChallenge."], function(response) { 
				document.getElementsByName('openotp_u2f')[0].value = JSON.stringify(response); 
				document.getElementById('loginForm').submit();
				}, $timeout ); 	}" . "\r\n";
		if( $u2fChallenge ) $overlay .= " else { 
				var u2f_activate = document.getElementById('u2f_activate'); 
				u2f_activate.innerHTML = '[Not Supported]'; 
				u2f_activate.style.color='red'; 
				} " . "\r\n"; */
        
        //if( $u2fChallenge ) $overlay .= " if (typeof u2f !== 'object' || typeof u2f.sign !== 'function'){ var u2f_activate = document.getElementById('u2f_activate'); u2f_activate.innerHTML = '[Not Supported]'; u2f_activate.style.color='red'; }" . "\r\n";
        //if( $u2fChallenge ) $overlay .= " else {  u2f.sign([".$u2fChallenge."], function(response) { document.getElementsByName('openotp_u2f')[0].value = JSON.stringify(response); document.getElementById('loginForm').submit(); }, $timeout ); }" . "\r\n";

        return $overlay;
    }
    
    private function soapRequest()
    {

        $options = ['location' => $this->server_url];
        if ($this->proxy_host != null && $this->proxy_port != null) {
            $options['proxy_host'] = $this->proxy_host;
            $options['proxy_port'] = $this->proxy_port;
            if ($this->proxy_username != null && $this->proxy_password != null) {
                $options['proxy_login'] = $this->proxy_username;
                $options['proxy_password'] = $this->proxy_password;
            }
        }
        
        $stream_context = stream_context_create(['ssl' => ['verify_peer' => false]]);
        if ($stream_context) {
            $options['stream_context'] = $stream_context;
        }
        
        ini_set('soap.wsdl_cache_enabled', '0');
        ini_set('soap.wsdl_cache_ttl', '0');
        
        try {
            //$soap_client = $this->soapClientTimeoutFactory->create(array($this->etcModuleDir.'/openotp.wsdl', $options));
            $soap_client = new \RCDevs\OpenOTP\Helper\SoapClientTimeout($this->etcModuleDir.'/openotp.wsdl', $options);
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        if (!$soap_client) {
            return false;
        }
        $soap_client->setTimeout(30);
        $soap_client->setVersion(2);
        $this->soap_client = $soap_client;
        
        return true;
    }
        
    public function openOTPSimpleLogin($username, $domain, $password, $remote_add, $context)
    {
        if (!$this->soapRequest()) {
            return false;
        }
        try {
            $resp = $this->soap_client->openotpSimpleLogin($username, $domain, $password, $this->client_id, $remote_add, $this->client_settings, null, $context);
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return $resp;
    }
    
    public function openOTPChallenge($username, $domain, $state, $password, $u2f)
    {
        if (!$this->soapRequest()) {
            return false;
        }
        try {
            $resp = $this->soap_client->openotpChallenge($username, $domain, $state, $password, $u2f);
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
        return $resp;
    }
    
    public function setEtcModuleDir($dir)
    {
        $this->etcModuleDir = $dir;
    }
    
    public function setServer_url($server_url)
    {
        $this->server_url = $server_url;
    }

    public function getServer_url()
    {
        return $this->server_url;
    }

    public function setClient_id($client_id)
    {
        $this->client_id = $client_id;
    }

    public function getClient_id()
    {
        return $this->client_id;
    }

    public function setDefault_domain($default_domain)
    {
        $this->default_domain = $default_domain;
    }

    public function getDefault_domain()
    {
        return $this->default_domain;
    }

    public function setClient_settings($client_settings)
    {
        $this->client_settings = $client_settings;
    }

    public function getClient_settings()
    {
        return $this->client_settings;
    }
    
    public function setProxy_host($proxy_host)
    {
        $this->proxy_host = $proxy_host;
    }

    public function getProxy_host()
    {
        return $this->proxy_host;
    }

    public function setProxy_port($proxy_port)
    {
        $this->proxy_port = $proxy_port;
    }

    public function getProxy_port()
    {
        return $this->proxy_port;
    }
    
    public function setProxy_login($proxy_login)
    {
        $this->proxy_login = $proxy_login;
    }

    public function getProxy_login()
    {
        return $this->proxy_login;
    }
    
    public function setProxy_password($proxy_password)
    {
        $this->proxy_password = $proxy_password;
    }

    public function getProxy_password()
    {
        return $this->proxy_password;
    }
    
    public function getContext_name()
    {
        return $this->context_name;
    }
    public function getContext_size()
    {
        return $this->context_size;
    }
    public function getContext_time()
    {
        return $this->context_time;
    }
}
