window.addEventListener('DOMContentLoaded', function () {
  var init_crop = function($uploadimg) {   
    var cropper;      
    var cropBoxData;
    var canvasData;
    
    var img_post_url = path_uploader;
    var uploadName   = $($uploadimg).attr('data-upload-name');     
    var cropWidth    = $($uploadimg).attr('data-crop-width');     
    var cropHeight   = $($uploadimg).attr('data-crop-height');  
    var aspectRatio  = $($uploadimg).attr('data-aspect-ratio');
    var uploadIndex  = $($uploadimg).attr('data-upload-index');

    var image = document.getElementById('image-' + uploadIndex);
    var input = document.getElementById('input-' + uploadIndex);
    var $progress = $('.img-upload-wrapper-index-' + uploadIndex + ' .progress');
    var $progressBar = $('.img-upload-wrapper-index-' + uploadIndex + ' .progress-bar');
    var $alert = $('.img-upload-wrapper-index-' + uploadIndex + ' .alert');
    var $modal = $('.img-upload-wrapper-index-' + uploadIndex + ' #modal');

    $('[data-toggle="tooltip"]').tooltip();

    input.addEventListener('change', function (e) {
      var files = e.target.files;
      var done = function (url) {
        input.value = '';
        image.src = url;
        $alert.hide();
        $modal.modal('show');
      };
      var reader;
      var file;
      var url;

      if (files && files.length > 0) {
        file = files[0];

        if (URL) {
          done(URL.createObjectURL(file));
        } else if (FileReader) {
          reader = new FileReader();
          reader.onload = function (e) {
            done(reader.result);
          };
          reader.readAsDataURL(file);
        }
      }
    });

    $('.img-upload-wrapper-index-' + uploadIndex + ' #modal').on('shown.bs.modal', function () {
      cropper = new Cropper(image, {
        autoCropArea:0.9,
		cropBoxResizable: false,
        aspectRatio: aspectRatio,
        ready: function () {
          //Should set crop box data first here
          cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);
        },
        zoom: function(e) {
          if (e.target.id === 'ZoomInBtn') {
            cropper.zoom(0.1);
          }
          if (e.target.id === 'ZoomOutBtn') {
            cropper.zoom(-0.1);
          }
        },          
      });

      var _data = {autoCropArea:0.9};
      cropper.setData(_data);
    }).on('.img-upload-wrapper-index-' + uploadIndex + ' hidden.bs.modal', function () {      
      cropBoxData = cropper.getCropBoxData();
      canvasData = cropper.getCanvasData();
      cropper.destroy();      
    });    
  
    document.getElementById('ZoomInBtn-' + uploadIndex).addEventListener('click', function () {
      cropper.zoom(0.1);
    });

    document.getElementById('ZoomOutBtn-' + uploadIndex).addEventListener('click', function () {
      cropper.zoom(-0.1);
    });

    document.getElementById('crop-' + uploadIndex).addEventListener('click', function () {
      var seperator = '?';
      if (img_post_url.includes("?")) {
        seperator = '&';
      }

      img_post_url = img_post_url+seperator+'action=uploadimage&actionIdForUpload='+actionIdForUpload+'&ajax=1&adminupload='+adminupload;
      var initialAvatarURL;
      var canvas;

      $modal.modal('hide');

      if (cropper) {
           canvas = cropper.getCroppedCanvas({
          width: cropWidth,
          height: cropHeight,
        });
        initialAvatarURL = uploadimg.src;
        uploadimg.src = canvas.toDataURL();
        $progress.show();
        $alert.removeClass('alert-success alert-warning');
        
        canvas.toBlob(function (blob) {
          // FormData is a built-in javascript object
          var formData = new FormData();    
          formData.append(uploadName, blob);

          $.ajax({
              url: img_post_url, // name of the file which we will be creating soon
              method: "POST",
              data: formData,
              processData: false,
              contentType: false,
      
              xhr: function () {
                var xhr = new XMLHttpRequest();

                xhr.upload.onprogress = function (e) {
                  var percent = '0';
                  var percentage = '0%';

                  if (e.lengthComputable) {
                    percent = Math.round((e.loaded / e.total) * 100);
                    percentage = percent + '%';
                    $progressBar.width(percentage).attr('aria-valuenow', percent).text(percentage);
                  }
                };

                return xhr;
              },

              success: function (data) {                
                $('#wk_mp_form_error').hide();
                $alert.show().addClass('alert-success').removeClass('hide');
                            
                if ($('#image-list-wrapper').length) {
                  json = JSON.parse(data);
                  $('#image-list-wrapper').html(json.tpl);
  
                  $('#imageTable .covered').each(function(index) {
                    var covered_icon_src = '/modules/marketplace/views/img/' + $(this).attr('src');
                    $(this).attr('src', covered_icon_src);
                  });
                } else {                                                              
                  $('.'+uploadName+'_wrapper img.old_img').attr('src', uploadimg.src);                  
                }       

                $('#uploadimg').attr('src', '/modules/marketplace/views/img/upload-img.png');
              },
  
              error: function () {
                uploadimg.src = initialProductimagesURL;
                $alert.show().addClass('alert-warning').text('Upload error');
              },   
              
              complete: function () {
                $progress.hide();                
              },              
          });

        }, 'image/jpg');
      
      }
    });
  };

  if (document.getElementById('uploadimg')) {
    init_crop(document.getElementById('uploadimg'))
  }
  
  if (document.getElementById('uploadimg1')) {
    init_crop(document.getElementById('uploadimg1'))
  }    

  if (document.getElementById('uploadimg2')) {
    init_crop(document.getElementById('uploadimg2'))
  }      
  
  if (document.getElementById('uploadimg3')) {
    init_crop(document.getElementById('uploadimg3'))
  }           

});