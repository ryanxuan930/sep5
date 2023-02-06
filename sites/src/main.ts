import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createI18n } from 'vue-i18n'

import App from './App.vue';
import router from './router';

import './assets/main.scss';

const datetimeFormats = {
'en-US': {
    short: {
      year: 'numeric', month: 'short', day: 'numeric'
    },
    long: {
      year: 'numeric', month: 'long', day: 'numeric',
      weekday: 'long', hour: 'numeric', minute: 'numeric', second: 'numeric'
    }
  },
  'zh-TW': {
    short: {
      year: 'numeric', month: 'short', day: 'numeric'
    },
    long: {
        year: 'numeric', month: 'long', day: 'numeric',
        weekday: 'long', hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true
      }
  }
}

const app = createApp(App);
const i18n = createI18n({
  datetimeFormats,
});

app.use(createPinia());
app.use(router);
app.use(i18n);

app.mount('#app');
