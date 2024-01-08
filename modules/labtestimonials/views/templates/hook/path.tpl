{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<style>
.lab-blog-nav{
	margin-bottom:10px;
}
.lab-blog-menu{
	position: relative;
	margin-bottom: 20px;
	padding: 0 10px;
	border: solid 1px #d3d8db;
	background-color: #fff;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	list-style: none;
}
.lab-blog-menu li{
	float:left;
}

.lab-blog-menu li:hover,.lab-blog-menu li.active {
	background: #333;
}

.lab-blog-menu li a{
	color: #757575;
    display: block;
    padding: 10px 20px;
    text-decoration: none !important;
}
.lab-blog-menu li:hover a,.lab-blog-menu li.active a{
	color: #fff;
	text-dicoration:none;
}
</style>
<div class="lab-blog-nav">
	<ul class="lab-blog-menu clearfix">
		<li class="{if  $view == 'items'}active{/if}">
			<a href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=labtestimonials&view=items">Items</a>
		</li>
		<li class="{if  $view == 'config'}active{/if}">
			<a href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=labtestimonials&view=config">Config</a>
		</li>
	</ul>
</div>