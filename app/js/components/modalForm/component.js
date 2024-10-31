import $ from 'jquery';
import cropper from 'cropperjs';
import jqueryCropper from 'jquery-cropper';

import template from './template.pug';

export default {
  name: 'modalform',
  template,
  props: {
    data: {
      type: Object,
      default() { return {}; },
    },
    cropData: {
      type: Object,
      default() { return {}; },
    },
    selector: {
      type: String,
      default: '#croper-image',
    },
  },
  data() {
    return {
      imageUrl: this.data.image,
      imageType: this.data.type,
      imageWidth: this.data.width,
      imageHeight: this.data.height,
      id: this.data.id,
      sizes: this.data.sizes,
      current: this.data.sizes[0],
    };
  },
  mounted() {
    $('.yraci_crop .ui-dialog-title').html(`${$('.yraci_crop .ui-dialog-title').html()} Original size: ${this.imageWidth}x${this.imageHeight}`);
    const options = {
      aspectRatio: this.current.ratio,
      viewMode: 1,
      zoomable: false,
      preview: '.img-preview',
      crop(e) {
      },
    };
    $(this.selector).on({
      crop(e) {
        $('#old-image').attr('style', $('#new-image').attr('style'));
      },
    }).cropper(options);
  },
  methods: {
    changeSize(event) {
      const index = Number($(event.target).attr('data-current'));
      this.current = this.sizes[index];
      const options = {
        aspectRatio: this.current.ratio,
        viewMode: 1,
        zoomable: false,
        preview: '.img-preview',
        crop(e) {
        },
      };
      $(this.selector).cropper('destroy').cropper(options);
      $('#old-image').attr('src', `${this.current.url}?${Math.random()}`);
    },
    reset(event) {
      const options = {
        aspectRatio: this.current.ratio,
        viewMode: 1,
        zoomable: false,
        preview: '.img-preview',
        crop(e) {
        },
      };
      $(this.selector).cropper('destroy').attr('src', this.imageUrl).cropper(options);
    },
    upload(event) {
      const files = event.target.files;
      let file;
      const URL = window.URL || window.webkitURL;
      if (files && files.length) {
        file = files[0];
        let uploadedImageName;
        let uploadedImageType;
        let uploadedImageURL;
        if (/^image\/\w+$/.test(file.type)) {
          uploadedImageName = file.name;
          uploadedImageType = file.type;
          if (uploadedImageURL) {
            URL.revokeObjectURL(uploadedImageURL);
          }
          uploadedImageURL = URL.createObjectURL(file);
          const options = {
            aspectRatio: this.current.ratio,
            viewMode: 1,
            zoomable: false,
            preview: '.img-preview',
            crop(e) {
            },
          };
          $(this.selector).cropper('destroy').attr('src', uploadedImageURL).cropper(options);
          $(event.target).val('');
        } else {
          window.alert('Please choose an image file.');
        }
      }
    },
    save(event) {
      const loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      const options = {
        width: this.current.width,
        height: this.current.height,
        imageSmoothingQuality: 'high',
        fillColor: '#fff',
      };
      const result = $(this.selector).cropper('getCroppedCanvas', options);
      const base64String = result.toDataURL(this.imageType);
      const request = {
        action: 'saveCropedImage',
        base64String,
        id: this.id,
        size: this.current.name,
        cropInfo: $(this.selector).cropper('getData', options),
      };
      $.post(ajaxurl, request, (response) => {
        if (response.error) {
          console.log(response);
          loader.hide();
        } else {
          $('#old-image').attr('src', `${response.url}?${Math.random()}`);
          loader.hide();
        }
      });
    },
  },
};
