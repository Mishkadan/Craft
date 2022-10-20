import store from './_store';
import Vue from 'vue';
import vClickOutside from 'v-click-outside';
import Postbox from './index.vue';

Vue.use(vClickOutside);

export default function() {
    new Vue({
        store,
        render: h => h(Postbox)
    }).$mount('.joms-postbox');
    const insertPb = document.createElement('script');
    insertPb.setAttribute('src', '/components/com_community/assets/postbox/js/views/postbox/postmenu.js?'+ Date.now());
    document.body.appendChild(insertPb);
}