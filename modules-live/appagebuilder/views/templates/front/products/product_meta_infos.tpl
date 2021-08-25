	<div class="end_img_container">
		<div {if $product.mp_seller_info.is_partner} class="img_art_container partner_profile col-md-6 float-md-left" {else}class="img_art_container  col-md-6 float-md-left"{/if}>
			<img class="img-fluid" src="{$product.mp_seller_info.profile_image}">
		</div>
		<div class="prod_price_container col-md-6 float-md-right"> 
			<span class="prix_act">
			{if $product.price_amount == 0}
			<span class="price_big">gratuit</span>
			{else}
			à partir de  <span class="price_big">{$product.price}</span> /personne  
			{/if}
			</span>
		</div>
	</div>
</div>
<div class="product_name_h3">
  <h3 class="h3 product-title" itemprop="name"><a href="{$product.url}">{$product.name}</a></h3>
</div>
<div class="meta_infos">
	<div class="dis_inline loc_container"><span class="fa fa-map-marker">{$product.mp_seller_info.city_name}</span></div> | 
	<div class="dis_inline time_container"><span class="fa fa-clock-o"> {$product.date_add}</span>
</div>
