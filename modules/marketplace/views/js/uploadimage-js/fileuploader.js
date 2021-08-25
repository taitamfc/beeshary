$(document).ready(function() {
	phpistUploadCropper('.sellerprofileimage_wrapper', '#sellerprofileimage');
	phpistUploadCropper('.shopimage_wrapper', '#shopimage');
	phpistUploadCropper('.profilebannerimage_wrapper', '#profilebannerimage');
	phpistUploadCropper('.shopbannerimage_wrapper', '#shopbannerimage');
});

function phpistUploadCropper(selector_wrapper, selector) {
	let cropper = '', upload = document.querySelector(selector), save = document.querySelector(selector_wrapper +' .save_cropped'), cropped = document.querySelector(selector_wrapper +' .img_cropped'), img_result = document.querySelector(selector_wrapper +' .img-result'), result = document.querySelector(selector_wrapper +' .cropped_img_result');
	// on change show image with crop options
	upload.addEventListener('change', (e) => {
	  if (e.target.files.length) {
			// start file reader
	    const reader = new FileReader();
	    reader.onload = (e)=> {
	      if(e.target.result){
			// create new image
			let img = document.createElement('img');
			img.id = 'image'+$(selector).attr('data-uploadfile');
			img.src = e.target.result;
			// remove old img
			$(selector_wrapper).find('img.old_img').hide();
			// clean result before
			result.innerHTML = '';
			// append new image
	        result.appendChild(img);
			// show save btn and options
			save.classList.remove('hide');
			// init cropper
			cropper = new Cropper(img, {
				autoCropArea: 1,
				dragMode: 'move',
		        aspectRatio: $(selector).attr('data-aspect-ratio'),
		        restore: false,
		        guides: false,
		        center: false,
		        highlight: false,
		        cropBoxMovable: false,
		        cropBoxResizable: false,
		        toggleDragModeOnDblclick: false
		    });
	      }
	    };
	    reader.readAsDataURL(e.target.files[0]);
	  }
	});

	// save on click
	save.addEventListener('click',(e)=>{
	  	e.preventDefault();
	  	// get result to data uri
	  	let imgSrc = cropper.getCroppedCanvas({
			width: $(selector).attr('data-img-w'),
			height: $(selector).attr('data-img-h')
		}).toDataURL();
	  	// remove hide class of img
	  	cropped.classList.remove('hide');
		img_result.classList.remove('hide');
		// show image cropped
	  	cropped.src = imgSrc;
	  	/*dwn.classList.remove('hide');
	  	dwn.download = 'imagename.png';
	  	dwn.setAttribute('href',imgSrc);*/
	  	$(selector_wrapper).find($(selector).attr('data-uploadfile')).val(imgSrc);
	});
}