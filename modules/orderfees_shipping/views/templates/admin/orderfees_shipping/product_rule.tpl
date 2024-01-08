{*
* Order Fees Shipping
*
*  @author    motionSeed <ecommerce@motionseed.com>
*  @copyright 2017 motionSeed. All rights reserved.
*  @license   https://www.motionseed.com/en/license-module.html
*}
<tr id="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_tr">
	<td>
		<input type="hidden" name="product_rule_{$product_rule_group_id|intval}[]" value="{$product_rule_id|intval}" />
		<input type="hidden" name="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_type" value="{$product_rule_type|escape}" />
		{* Everything is on a single line in order to avoid a empty space between the [ ] and the word *}
		[{if $product_rule_type == 'features'}{l s='Features:' mod='orderfees_shipping'}{elseif $product_rule_type == 'products'}{l s='Products:' mod='orderfees_shipping'}{elseif $product_rule_type == 'categories'}{l s='Categories:' mod='orderfees_shipping'}{elseif $product_rule_type == 'manufacturers'}{l s='Manufacturers:' mod='orderfees_shipping'}{elseif $product_rule_type == 'suppliers'}{l s='Suppliers' mod='orderfees_shipping'}{elseif $product_rule_type == 'attributes'}{l s='Attributes' mod='orderfees_shipping'}{/if}]
	</td>
	<td>
		<input type="text" id="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_match" value="" disabled="disabled" />
	</td>
	<td>
		<a class="btn btn-default" id="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_link" href="#product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_content">
			<i class="icon-list-ul"></i>
			{l s='Choose' mod='orderfees_shipping'}
		</a>
		<div>
			<div id="product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_content">
                            {$product_rule_choose_content|escape:'none':'UTF-8'}
			</div>
		</div>
	</td>
	<td class="text-right">
		<a class="btn btn-default" href="javascript:removeProductRule({$product_rule_group_id|intval}, {$product_rule_id|intval});">
			<i class="icon-remove"></i>
		</a>
	</td>
</tr>

<script type="text/javascript">
	$('#product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_content').parent().hide();
	$("#product_rule_{$product_rule_group_id|intval}_{$product_rule_id|intval}_choose_link").fancybox({
		autoDimensions: false,
		autoSize: false,
		width: 600,
		height: 250});
</script>