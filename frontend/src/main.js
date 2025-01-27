import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import '@vuepic/vue-datepicker/dist/main.css'
import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

// local imports
import App from './App.vue'
import router from './router'
import {i18n} from "@/i18n/index.js";

const app = createApp(App)
const vuetify = createVuetify({
    components,
    directives,
})

app.use(createPinia())
    .use(router)
    .use(vuetify)
    .use(i18n);

app.mount('#app')
