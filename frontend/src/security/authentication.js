import Keycloak from 'keycloak-js';
import {ca} from "vuetify/locale";

export const keycloakAdapter = new Keycloak({
    url: import.meta.env.VITE_KEYCLOAK_URL,
    realm: import.meta.env.VITE_KEYCLOAK_REALM,
    clientId: import.meta.env.VITE_KEYCLOAK_CLIENT_ID,
});

export async function isAuthenticated() {
    try {
        //check locally on server before oauth server
        return await keycloakAdapter.init(
            {
                onLoad: 'login-required',
            }
        );
    } catch (error) {
        return false;
    }
}

export async function secureFetch(url, options = {}) {
    //update the token if it has at most 30 seconds validity left.
    try {
        await keycloakAdapter.updateToken(30);
    } catch (error) {
        console.log('Failed to refresh token');
    }

    if (!await isAuthenticated()) {
       return ;
    }

    options.headers = {
        ...options.headers,
        'Authorization': `Bearer ${keycloakAdapter.token}`
    }
    return fetch(url, options);
}