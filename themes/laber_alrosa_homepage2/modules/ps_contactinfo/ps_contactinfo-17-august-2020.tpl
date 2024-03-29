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

<div class="block-contact col-lg-3 col-md-3 col-sm-6 col-xs-12 links wrapper">
	{hook h="logoFooter"}
    <p class="h3 text-uppercase block-contact-title">{l s='Store information' d='Shop.Theme.Global'}</p>
	<div class="title clearfix hidden-md-up" data-target="#footer_sub_menu_contactFooter" data-toggle="collapse">
		<span class="h3">{l s='Store information' d='Shop.Theme.Global'}</span>
		<span class="float-xs-right">
		  <span class="navbar-toggler collapse-icons">
			<i class="material-icons add">&#xE313;</i>
			<i class="material-icons remove">&#xE316;</i>
		  </span>
		</span>
	</div>
	<div id="footer_sub_menu_contactFooter" class="collapse">
	  {if $contact_infos.address.address1}
		<p class="address address1"><i class="pe-7s-map-marker"></i> {$contact_infos.address.address1}</p>
	  {/if}
	   {*{if $contact_infos.address.address2}
		<p class="address address2">{$contact_infos.address.address2}</p>
	  {/if}*}
      {if $contact_infos.phone}
        <p class="phone">
		<i class="pe-7s-call"></i>
        {* [1][/1] is for a HTML tag. *}
        {l s='Call us: [1]%phone%[/1]'
          sprintf=[
          '[1]' => '<span>',
          '[/1]' => '</span>',
          '%phone%' => $contact_infos.phone
          ]
          d='Shop.Theme.Global'
        }
		</p>
      {/if}
      {if $contact_infos.fax}
        <p class="fax">
		<i class="pe-7s-phone"></i>
        {* [1][/1] is for a HTML tag. *}
        {l
          s='Fax: [1]%fax%[/1]'
          sprintf=[
            '[1]' => '<span>',
            '[/1]' => '</span>',
            '%fax%' => $contact_infos.fax
          ]
          d='Shop.Theme.Global'
        }
		</p>
      {/if}
	{if $contact_infos.email}
        <p class="email">
		<i class="pe-7s-mail"></i>
        {* [1][/1] is for a HTML tag. *}
        {l
          s='Email us: [1]%email%[/1]'
          sprintf=[
            '[1]' => '<a href="mailto:'|cat:$contact_infos.email|cat:'" class="dropdown">',
            '[/1]' => '</a>',
            '%email%' => $contact_infos.email
          ]
          d='Shop.Theme.Global'
        }
		</p>
      {/if} 
  </div>  
</div>
