{if $is_seller}
<script>
	document.getElementsByTagName('body')[0].classList.add("is_seller");
</script>
{/if}
{if $is_partner}
<script>
	document.getElementsByTagName('body')[0].classList.add("is_partner");
</script>
{/if}
{if $cr_is_seller}
<script>
	document.getElementsByTagName('body')[0].classList.add("cr_is_seller");
</script>
{/if}
{if $cr_is_partner}
<script>
	document.getElementsByTagName('body')[0].classList.add("cr_is_partner");
</script>
{/if}
{if $partner_color}
<style>
#product .seller_img_prod.is_partner_img2 .wk-shop-default-icon {
    border: 4px solid {$partner_color};
    background: {$partner_color};
}
.img_art_container.partner_profile {
    border: 3px solid {$partner_color} !important;
    background: {$partner_color};
}
.wk-mp-block .wk_profile_img.partner_profile {
    border: 5px solid {$partner_color} !important;
    background: {$partner_color};
}
.img_store_center.is_partner_img img {
	border: 3px solid {$partner_color};
	background: {$partner_color} !important;
}
</style>
{/if}