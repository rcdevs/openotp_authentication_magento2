# RCDevs OpenOTP Authentication Extension on Magento 2.0

Welcome to RCDevs OpenOTP Authentication on Magento 2.0 Installation! We're glad that you are interested in our extension.

<p>OpenOTP 2FA Authentication Plugin enables strong two-factor authentication for administrators to login on Magento Dashboard.  
It displays an overlay on Challenge-Response session, after fill in 
username and password. The plugin supports global and per user settings configuration. 
The plugin will transparently handle any OpenOTP Login Mode including:
<ul class="short-features">
<li>LDAP only</li>
<li>OTP only</li>
<li>LDAP+OTP</li>
<li>LDAP+U2F</li>
<li>LDAP+(OTP or U2F)</li>
</ul>

If configured, OpenOTP plugin is able to auto-create new account in Magento while login for the first time.</p>

<p><span style="color: #000000;"><strong>You need to install (if not already done) our openOTP Authentication Server </strong></span></p>
<span><a href="http://www.rcdevs.com/downloads/Software+Packages/" target="blank"> Stand Alone Packages </a></span>
<br><span><a href="http://www.rcdevs.com/downloads/VMWare+Appliances/" target="blank"> Appliances (to be configured before Prods!) </a></span></br>


To ensure that cache will not cause any problem, you'd better turn it off. This can be done from the admin console by navigating to the Cache Management page (System->Cache Management), 
selecting all caches, clicking "disable" from the drop-down menu, and submitting the change.

You also should run the Magento software in developer mode when you’re extending or customizing it. You can use this command line to show current mode :

`php bin/magento deploy:mode:show`

Use this command to change to developer mode :

`php bin/magento deploy:mode:set developer`


<h2>1a - Install from composer (Github)</h2>

On your magento2 installation edit the composer.json available in the root folder. Add the line to "repositories" entry in the composer.json 
"repositories": [ { "type": "vcs", "url": "https://github.com/rcdevs/openotp_authentication_magento2" } ],
Open terminal and type the command 

`composer require rcdevs/openotp`

it must match packagist name see https://packagist.org/packages/rcdevs/openotp.

<h2>1b - Install by uploading files</h2>

You can download as "zip" file and unzip OpenOTP extension or clone this repository by the following commands:

Use SSH: git clone git@github.com:rcdevs/openotp_authentication_magento2.git

Use HTTPS: git clone https://github.com/rcdevs/openotp_authentication_magento2.git

When you have completed, you will have a folder containing all files of this extension. 
Then, please create the folder **[magento 2 root folder]/app/code/RCDevs/OpenOTP** /!\ Case sensitive /!\ and copy all files which you have downloaded to that folder.


<h2>2 - Magento CLI final Install steps</h2>
Open a terminal application, change to magento root directory and use command line :

`cd [magento 2 root folder]`
`php bin/magento setup:upgrade`

Wait a second to complete installation process.
After that, if your website is in the production mode, please run the command:

`php bin/magento setup:static-content:deploy`

