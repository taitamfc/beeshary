{**
 * 2017-2018 PHPIST.
 *
 *  @author    Yassine belkaid <yassine.belkaid87@gmail.com>
 *  @copyright 2017-2018 PHPIST
 *  @license   https://store.webkul.com/license.html
 *}

<div id="sellerImagesForm" style="display: none;">
	<div class="prv_section">
		<a class="prv_store" href="javascript:void(0);">
			<img src="{$smarty.const._THEME_IMG_DIR_}bee-fleche.svg" />
		</a>
	</div>
	<div class="form-group">
        <img class="center-block store_top_pic" src="{$urls.base_url}themes/beeshary_child/assets/img/picto-boutique.jpg" />
        <div class="pp_seller_profile_title">{l s='My store' mod='mpsellerwiselogin'}</div>
        <div class="pp_seller_profile_subtitle">Sélectionnez des images qui vous ressemblent pour présenter votre univers et vos produits.</div>
    </div>
	<div class="form-group image_wrapper">
		<div class="input_left">
			<label for="profile_image" class="custom-file-upload" {**onClick="getFile('profile_image')"**}>
			    Ajouter une photo de profil
			</label>
			<input id="profile_image" name="profile_image" type="file" onchange="displayLogoImg('profile_image')" />
			<div class="image_preview"></div>
			<div class="image_banner_text">
				<p>Il faut fournir une image de 300px par 300px</p>
			</div>
		</div>
	
		<div class="input_right">
			<label for="shop_logo" class="custom-file-upload">
			    Ajouter votre logo
			</label>
			<input id="shop_logo" name="shop_logo" type="file" onchange="displayLogoImg('shop_logo')" />
			<div class="image_preview"></div>
			<div class="image_banner_text">
				<p>Il faut fournir une image de 300px par 300px</p>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group image_wrapper">
		<label class="banner_img">{**l s='Banner image' mod='mpsellerwiselogin'***}Bannière et illustration</label>
		<div class="cleatfix"></div>
		<div class="input_left">
			<label for="profile_banner" class="custom-file-upload">
			    Photo de votre savoir-faire
			</label>
			<input id="profile_banner" name="profile_banner" type="file" onchange="displayBannerImg('profile_banner')" />
			<div class="image_banner_preview"></div>
			<div class="image_banner_text">
				<p>Il faut fournir une image de 1538px par 380px</p>
			</div>
		</div>
	
		<div class="input_right">
			<label for="shop_banner" class="custom-file-upload">
			    Ajouter une photo à votre boutique
			</label>
			<input id="shop_banner" name="shop_banner" type="file" onchange="displayBannerImg('shop_banner')" />
			<div class="image_banner_preview"></div>
			<div class="image_banner_text">
				<p>Il faut fournir une image de 750px par 750px</p>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<a id="submitImages" href="#" class="next_btn">{l s='Next' mod='mpsellerwiselogin'}</a>
	</div>
</div> 
