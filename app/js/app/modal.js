/* eslint-disable */
import $ from 'jquery';
import cropper from 'cropperjs';
import jquery_cropper from 'jquery-cropper';

import ModalContent from './modalContent';

class Modal {
  constructor() {
    this.initModal();
    this.getModalContent();
  }
  
  getUrlParameter(url, sParam) {
    const sURLVariables = url.split('?')[1].split('&')
    let sParameterName
    let i

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
  }
  getModalContent() {
    const _this = this
    $(document).on('click', '.yraci_crop-image', function() {
      let attach_id = $(this).parents('.attachment-details').attr('data-id')
      let url
      if (!attach_id) {
        url = $('.edit-attachment').attr('href')
        if (url) {
          attach_id = _this.getUrlParameter(url, 'post')
        } else {
          url = $(".actions a[href*='post=']").attr('href')
          if (url) {
            attach_id = _this.getUrlParameter(url, 'post')
          }
        }
      }
      const data = {
        action: 'getModalContent',
        id: attach_id,
      };
      $.post(ajaxurl, data, function(data) {
        if (data.error) {
        } else {
          const vueModules = new ModalContent('[vue-app]', data);
          $('#yraci_crop-dialog').dialog('open');
        }
      });
    });
  }

  initModal() {
    $('#yraci_crop-dialog').dialog({
        title: 'Crop image.',
        dialogClass: 'yraci_crop',
        autoOpen: false,
        draggable: false,
        width: '94%',
        modal: true,
        resizable: false,
        closeOnEscape: true,
        position: {
          my: "center",
          at: "center",
          of: window
      },
      open: function () {
        // close dialog by clicking the overlay behind it
        $('.ui-widget-overlay').on('click', function(){
          $('#yraci_crop-dialog').dialog('close');
        })
      },
      create: function () {
        // style fix for WordPress admin
        $('.ui-dialog-titlebar-close').addClass('ui-button');
      },
      beforeClose: function( event, ui ) {
        $('[vue-app]').html('<modalform :data="data"></modalform>');
      },
    });
  }

}

export default Modal;
