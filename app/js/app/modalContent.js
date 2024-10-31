import Vue from 'vue';
// Import component
import loading from 'vue-loading-overlay';

import ModalForm from './../components/modalForm/component';

// Init plugin
Vue.use(loading);

class ModalContent {
  constructor(selector, data) {
    this.selector = selector;
    this.element = document.querySelectorAll(selector);
    if (this.element && this.element.length > 0) {
      this.init(data);
    }
  }

  init(data) {
    this.element.forEach((item, index) => {
      const tmp = new Vue({
        el: item,
        components: {
          modalform: ModalForm,
        },
        data: {
          data,
        },
      });
    });
  }
}

export default ModalContent;
