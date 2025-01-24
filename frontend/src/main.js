import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import '@vuepic/vue-datepicker/dist/main.css'
import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { createAuth0 } from '@auth0/auth0-vue';


import App from './App.vue'
import router from './router'

const app = createApp(App)
const vuetify = createVuetify({
    components,
    directives,
})

const auth0 = createAuth0(
    {
        domain: "dev-q5q337ag5psljqaf.us.auth0.com",
        clientId: "JRsKtH3qOiiAOPO3PAviVjvCkNDtDnfv",
        authorizationParams: {
            redirect_uri: window.location.origin
        }
    }
);

app.use(createPinia())
app.use(router)
app.use(vuetify);
app.use(auth0);

app.mount('#app')
