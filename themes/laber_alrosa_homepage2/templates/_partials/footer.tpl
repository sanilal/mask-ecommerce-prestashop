{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="laberFooter-top">
	<div class="container">
	  <div class="row">
		{hook h='displayFooterBefore'}
	  </div>
	</div>
</div>

<div class="laberFooter-center">
  <div class="container">
    <div class="row">
      {hook h='displayFooter'}
    </div>
  </div>
</div>
<div class="laberFooter-center2">
  <div class="container">
    <div class="row">
      {hook h='displayFooter2'}
    </div>
  </div>
</div>
<div class="laberFooter-bottom">
	<div class="container">
		<div class="row">
		  {hook h='displayFooterAfter'}
		</div>
	</div>
</div>
<div class="laber_alert_compare">
	<div class="laber_add_to_compare alert_notice">
		<div class="loader_content"></div>
	</div>
</div>
<!-- GetButton.io widget -->
<script type="text/javascript">
    (function () {
        var options = {
            facebook: "102124681610925", // Facebook page ID
            whatsapp: "+971552201294", // WhatsApp number
            call_to_action: "Message us", // Call to action
            button_color: "#2c2c2c", // Color of button
            position: "right", // Position may be 'right' or 'left'
            order: "facebook,whatsapp", // Order of buttons
        };
        var proto = document.location.protocol, host = "getbutton.io", url = proto + "//static." + host;
        var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';
        s.onload = function () { WhWidgetSendButton.init(host, proto, options); };
        var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
    })();
</script>
<!-- /GetButton.io widget -->