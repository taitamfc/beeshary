{*
* 2010-2017 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*}
<div class="img-upload-wrapper img-upload-wrapper-index-{$index}">
	<!--<h1>{l s='Select an image' mod='marketplace'}</h1>-->
	<h3>{l s='Sélectionner une image' mod='marketplace'}</h3>
	<label class="label" data-toggle="tooltip" title="{l s='Select an image' mod='marketplace'}">
		<img class="rounded upload-img" id="uploadimg{$index}" src="/modules/marketplace/views/img/upload-img.png" alt="image"
			data-upload-name="{$uploadName}"
			data-crop-width="{$cropWidth}"
			data-crop-height="{$cropHeight}"
			data-aspect-ratio="{$aspectRatio}"
			data-upload-index={$index}
		>
		<input type="file" class="sr-only" id="input-{$index}" name="image" accept="image/*">
	</label>
	<div class="progress">
		<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
	</div>
	<!--<div class="alert hide" role="alert">{l s='Upload success' mod='marketplace'}</div>-->
	<div class="alert hide" role="alert">{l s='Téléchargée avec succès' mod='marketplace'}</div>
	<div class="modal fade crop-modal" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
			<!--<h5 class="modal-title" id="modalLabel">{l s='Crop the image' mod='marketplace'}</h5>-->
			<h5 class="modal-title" id="modalLabel">{l s='Recadrer l\'image' mod='marketplace'}</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			</div>
			<div class="modal-body">
			<div class="img-container">
				<img id="image-{$index}" src="https://avatars0.githubusercontent.com/u/3456749">
			</div>
			</div>
			<div class="modal-footer">
				<div class="btn-group">
					<button id="ZoomInBtn-{$index}" type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
					  <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(0.1)">
						<span class="fa fa-search-plus">+</span>
					  </span>
					</button>
					<button id="ZoomOutBtn-{$index}" type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
					  <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(-0.1)">
						<span class="fa fa-search-minus">-</span>
					  </span>
					</button>
				</div>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
				<!--{l s='Cancel' mod='marketplace'}-->
				{l s='Annuler' mod='marketplace'}
				</button>
				<button type="button" class="btn btn-primary" id="crop-{$index}">
				<!--{l s='Crop' mod='marketplace'}-->
				{l s='Recadrer' mod='marketplace'}
				</button>
			</div>
		</div>
		</div>
	</div>
	<p class="_info_"><em>Une largeur minimale de {$cropWidth} px est recommandée</em></p>
</div>

