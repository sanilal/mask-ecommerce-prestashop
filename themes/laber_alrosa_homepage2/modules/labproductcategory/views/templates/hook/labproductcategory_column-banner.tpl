{**
* 2007-2015 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{foreach from=$group_cat_result item=group_cat name=group_cat_result}
	{assign var='id' value=$group_cat.id_labgroupcategory}
	{assign var='show_sub' value=$group_cat.show_sub}
	{assign var='use_slider' value=$group_cat.use_slider}
	{assign var='type_display' value=$group_cat.type_display}
{/foreach}
{$number_line = 1}
{$id_lang = Context::getContext()->language->id}
{if $use_slider != 1}
	{assign var='nbItemsPerLine' value=4}
	{assign var='nbItemsPerLineTablet' value=3}
	{assign var='nbItemsPerLineMobile' value=2}
{/if}
<!-- Module Product From Category -->
<div class="laberthemes">
	<div class="laberProductGrid labProductList">
		{foreach from=$group_cat_result item=group_cat name=group_cat_result}
			{$group_cat_info = $group_cat.cat_info}
			{foreach from=$group_cat_info item=cat_info name=g_cat_info}
			 
				 <div class="block-content">
					<div class="laberProdCategory labProdCat-{$cat_info.id_cat|intval}">
						<div class="title_block">
						  
							{if $cat_info.cat_icon!='' }
								<div class="icon_cat" style="background-color:{$cat_info.cat_color|escape:'html':'UTF-8'}">
								   <img src="{$icon_path|escape:'html':'UTF-8'}{$cat_info.cat_icon|escape:'html':'UTF-8'}" alt=""/>
								</div>
							{/if}
							<h3>
								<a href="{$link->getCategoryLink($cat_info.id_cat, $cat_info.link_rewrite)|escape:'html':'UTF-8'}" title="{$cat_info.cat_name|escape:'html':'UTF-8'}">
									<span>{$cat_info.cat_name|escape}</span>
								</a>
							</h3>
						  
						
						{if $show_sub}
						
							<ul class="sub_cates">
								{foreach from = $cat_info.sub_cat item=sub_cat name=sub_cat_info}
									<li><a href="{$link->getCategoryLink($sub_cat.id_category, $sub_cat.link_rewrite)|escape:'html':'UTF-8'}" title="{$sub_cat.name|escape:'html':'UTF-8'}">{$sub_cat.name|escape}</a></li>
								{/foreach}
								{if $cat_info.show_img == 1 && isset($cat_info.id_image) && $cat_info.id_image > 0}
								<li class="cat-img">
									<a href="{$link->getCategoryLink($cat_info.id_cat, $cat_info.link_rewrite)|escape:'html':'UTF-8'}" title="{$cat_info.cat_name|escape:'html':'UTF-8'}">
										<img src="{$link->getCatImageLink($cat_info.link_rewrite, $cat_info.id_image, 'medium_default')|escape:'html':'UTF-8'}"/>
									</a>
								</li>
								{/if}
								{if isset($cat_info.special_prod_obj) && count($cat_info.special_prod_obj)}
									{$cat_product = $cat_info.special_prod_obj}
									{$id_lang = Context::getContext()->language->id}
									<li class="lab-prod-special">
										<a class="product_img_link" href="{$link->getProductLink($cat_product)|escape:'html':'UTF-8'}" title="{$cat_product->name[$id_lang]|escape:'html':'UTF-8'}">
											<img class="replace-2x img-responsive" src="{$link->getImageLink($cat_product->link_rewrite[$id_lang], $cat_product->id_image, 'home_default')|escape:'html':'UTF-8'}" alt="" title="{$cat_product->name[$id_lang]|escape:'html':'UTF-8'}"/>
										</a>
									</li>
								{/if}
							</ul>
						
						{/if}
						</div>
						<div class="row">
							{if $cat_info.cat_banner!='' }
							<div class="laberCat-banner col-lg-3 col-md-3 col-sm-3 col-xs-12">
								<div class="cat-banner">
									<a href="{$link->getCategoryLink($cat_info.id_cat, $cat_info.link_rewrite)|escape:'html':'UTF-8'}" title="{$cat_info.cat_name|escape:'html':'UTF-8'}"><img src="{$banner_path|escape:'html':'UTF-8'}{$cat_info.cat_banner|escape:'html':'UTF-8'}" alt=""/></a>
								</div>
							</div>
							{/if}
							<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
								<div class="clearfix laberOverflow">
									<div class="product_list labContent row">
										
											<div class="owlProductCate-{$id}{$cat_info.id_cat|intval} {if $group_cat.use_slider != 1}row{/if}">
												{if isset($cat_info.product_list) && count($cat_info.product_list) > 0}
												{foreach from=$cat_info.product_list item=product name=product_list}
													{if $group_cat.use_slider != 1}
														<div class="item-inner col-lg-3 col-md-3 col-sm-4 col-xs-12
														{if $smarty.foreach.product_list.iteration%$nbItemsPerLine == 0} last-in-line
														{elseif $smarty.foreach.product_list.iteration%$nbItemsPerLine == 1} first-in-line{/if}
														{if $smarty.foreach.product_list.iteration%$nbItemsPerLineTablet == 0} last-item-of-tablet-line
														{elseif $smarty.foreach.product_list.iteration%$nbItemsPerLineTablet == 1} first-item-of-tablet-line{/if}
														{if $smarty.foreach.product_list.iteration%$nbItemsPerLineMobile == 0} last-item-of-mobile-line
														{elseif $smarty.foreach.product_list.iteration%$nbItemsPerLineMobile == 1} first-item-of-mobile-line{/if}
														">
													{else}
														{if $smarty.foreach.product_list.iteration % $number_line == 1 || $number_line == 1}
														<div class="item-inner ajax_block_product">
														{/if}
													{/if}
														{block name='product'}

															{include file="catalog/_partials/miniatures/product.tpl" product=$product}

														{/block}
													{if $use_slider != 1}
														</div>
													{else}
														{if $smarty.foreach.product_list.iteration % $number_line == 0 ||$smarty.foreach.product_list.last || $number_line == 1}
														</div>
														{/if}
													{/if}
												{/foreach}
												{/if}
											</div>
											{if $group_cat.use_slider == 1}
											<div class="owl-buttons">
												<div class="owl-prev prev-{$id}{$cat_info.id_cat|intval}"><i class="fa fa-angle-left"></i></div>
												<div class="owl-next next-{$id}{$cat_info.id_cat|intval}"><i class="fa fa-angle-right"></i></div>
											</div>
											{/if}
											{*{if count($cat_info)>0}
											<div class="manu-list">
												<ul>
													{foreach from=$cat_info.manufacture item=manu_item name=manufacture}
														<li><a href="#">{$manu_item->name|escape:'html':'UTF-8'}</a></li>
													{/foreach}
												</ul>
											</div>
											{/if}*}
										
									</div>
								</div>
							</div>
							
						</div>
						{if $group_cat.use_slider == 1}
							<script type="text/javascript">
								$(document).ready(function() {
									var owl = $(".owlProductCate-{$id}{$cat_info.id_cat|intval}");
									owl.owlCarousel({
										items :3,
										itemsDesktop : [1199,3],
										itemsDesktopSmall : [991,2],
										itemsTablet: [767,2],
										itemsMobile : [480,1],
										slideSpeed : 2000,
										paginationSpeed : 2000,
										rewindSpeed : 2000,
										autoPlay :  false,	
										stopOnHover: false,
										pagination : false,
										addClassActive : true,
									});
									$(".next-{$id}{$cat_info.id_cat|intval}").click(function(){
									owl.trigger('owl.next');
									})
									$(".prev-{$id}{$cat_info.id_cat|intval}").click(function(){
									owl.trigger('owl.prev');
									})
								});
							</script>
						{/if}
					</div>
					</div>
				

			{/foreach}
		{/foreach}
	</div>
</div>
<!-- /Module Product From Category -->