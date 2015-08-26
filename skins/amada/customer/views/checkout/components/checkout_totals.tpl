{* $Id$ *}

<div class="clear" id="checkout_totals">
	{if $cart_products}
		<div class="coupons-container">
			{if $cart.has_coupons}
				{include file="views/checkout/components/promotion_coupon.tpl" location=$location}
			{/if}
				
			{hook name="checkout:payment_extra"}
			{/hook}
		</div>
	{/if}
	
	{hook name="checkout:payment_options"}
	{/hook}
	
	{include file="views/checkout/components/checkout_totals_info.tpl"}
<!--checkout_totals--></div>