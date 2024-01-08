{**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<div class="item laber_{$product.id_product}">
<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
  <div class="laberProduct-container">
	  <div class="row">
		<div class="col-md-6">
			<div class="laberProduct-image">
				{block name='product_thumbnail'}
					{if $product.cover}
					  <a href="{$product.url}" class="thumbnail product-thumbnail">
						<img
						  src = "{$product.cover.bySize.home_default.url}"
						  alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
						  data-full-size-image-url = "{$product.cover.large.url}"
						/>
					  </a>
					{else}
					  <a href="{$product.url}" class="thumbnail product-thumbnail">
						<img
						  src = "{$urls.no_picture_image.bySize.home_default.url}"
						/>
					  </a>
					{/if}
				  {/block}
				{block name='product_flags'}
					<ul class="laberProduct-flags">
						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
							{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								<li class="laber-flag laber_reduction_percent_display">
									<span>
										{if $product.specific_prices && $product.specific_prices.reduction_type == 'percentage'}
											-{$product.specific_prices.reduction|escape:'quotes':'UTF-8' * 100}%
										{else}
											-{$product.price_without_reduction-$product.price|floatval}
										{/if}
									</span>
								</li>
							{/if}
						{/if}
					{foreach from=$product.flags item=flag}
						<li class="laber-flag laber-{$flag.type}"><span>{$flag.label}</span></li>
					{/foreach}
					</ul>
				{/block}
				{*{block name='product_variants'}
					{if $product.main_variants}
						{include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
					{/if}
				{/block}*}
			</div>
			{block name='product_thumbnail'}
				<div class="product_thumbnail">
					<ul>
						{foreach from=$product.images item=image name=image_list}
							  <li class="{if $image.id_image == $product.cover.id_image}active{/if} laberThumb-container" 
									data-src="{$image.bySize.large_default.url}">
									<img
									  src="{$image.bySize.small_default.url}"
									  alt="{$image.legend}"
									  title="{$image.legend}"
									  itemprop="image"
									/>
							  </li>
						{/foreach}
					</ul>
				</div>
			{/block}
		</div>
		<div class="col-md-6">
			<div class="laber-product-description">
				
				  {block name='product_name'}
					<h2 class="productName" itemprop="name"><a href="{$product.url}">{$product.name}</a></h2>
				  {/block}
				{hook h='displayProductListReviews' product=$product}
				{block name='product_price_and_shipping'}
					{if $product.show_price}
					  <div class="laber-product-price-and-shipping">
						<span itemprop="price" class="price">{$product.price}</span>
						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
							{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								<span class="reduction_percent_display">
									{if $product.specific_prices && $product.specific_prices.reduction_type == 'percentage'}
										-{$product.specific_prices.reduction|escape:'quotes':'UTF-8' * 100}%
									{else}
										-{$product.price_without_reduction-$product.price|floatval}
									{/if}
								</span>
							{/if}
						{/if}
						{if $product.has_discount}
						  {hook h='displayProductPriceBlock' product=$product type="old_price"}
						  <span class="regular-price">{$product.regular_price}</span>
						{/if}

						{hook h='displayProductPriceBlock' product=$product type="before_price"}

						{hook h='displayProductPriceBlock' product=$product type='unit_price'}

						{hook h='displayProductPriceBlock' product=$product type='weight'}
					  </div>
					{/if}
				  {/block}
					<div class="description_short">
						
							{$product.description_short|escape:'quotes':'UTF-8' nofilter}
						
					</div>
			
				
				
				
			  
				{hook h='PSProductCountdown' id_product=$product.id_product}
				
				<div class="laberCart">
						<form action="{$urls.pages.cart}" method="post">
						<input type="hidden" name="token" value="{$static_token}">
						<input type="hidden" value="{$product.id_product}" name="id_product">
						<button data-button-action="add-to-cart" class="laberBottom
						{if !$product.add_to_cart_url}
						  disabled
						{/if}
						"
						{if !$product.add_to_cart_url}
							disabled
						{/if}
						>
							<span>{l s='Add to cart' d='Shop.Theme.Actions'}</span>
						</button>
						</form>
				</div>
			</div>
		</div>
	  </div>
  </div>
</article>
	<script type="text/javascript">
		$(document).ready(function() {
				$('.laberSpecialProducts .laber_{$product.id_product} .product_thumbnail li').click(function(){
				$('.laberSpecialProducts .laber_{$product.id_product} .product_thumbnail li').removeClass('active');
				$(this).addClass('active');
				var _SRC = $(this).attr('data-src');
				$('.laberSpecialProducts .laber_{$product.id_product} .laberProduct-image .cover_image img').attr('src', _SRC);
			});
			
			$('.product_thumbnail ul').slick({
			  infinite: false,
			  dots: false,
			  variableWidth: true,
			  speed: 300,
			  slidesToShow: 4,
			  slidesToScroll: 1,
			  prevArrow:'<a class="slick-prev" href="javascript:void(0)"><i class="fa fa-angle-left"></i></a>',
			  nextArrow:'<a class="slick-next" href="javascript:void(0)"><i class="fa fa-angle-right"></i></a>'
			});
		});
	</script>
</div>