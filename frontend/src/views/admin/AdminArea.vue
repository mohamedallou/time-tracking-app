<script setup>
import {RouterLink, RouterView, useRoute, useRouter} from 'vue-router'
import {useAppStore} from "@/stores/app.store";
import {onMounted, ref} from "vue";

const router = useRouter();
const route = useRoute();


async function mounted() {
    const store = useAppStore();
}

onMounted(mounted);

const drawer = ref(null);

</script>

<template>
    <v-overlay v-model="useAppStore().loading" class="align-center justify-center">
        <div class="text-center">
            <v-progress-circular
                :size="100"
                color="primary"
                indeterminate
                width="10"
            ></v-progress-circular>
        </div>
    </v-overlay>
    <v-app>
        <v-app-bar
            color="primary"
            prominent
            app
        >
            <!--            <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>-->
            <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            <v-app-bar-title>Time Tracking</v-app-bar-title>
            <router-link :to="'/admin'">
                <v-btn variant="text" icon="mdi-home" ></v-btn>
            </router-link>
        </v-app-bar>
        <v-navigation-drawer
            v-model="drawer"
            location="left"
        >
            <v-list
                :model-value="$route.path"
            >
                <v-list-item
                    v-for="(item, index) in useAppStore().getMenuPoints"
                    :value="item.path"
                    class="pa-6"
                >
                    <router-link :to="item.path">
                        <v-list-item-title>
                            {{item.title}}
                        </v-list-item-title>
                    </router-link>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
        <v-main>
            <v-container fluid class="h-100">
                <RouterView />
                <v-snackbar
                    :color="useAppStore().snackType"
                    v-model="useAppStore().showSnack"
                    location="top"
                >
                    <div class="text-subtitle-1 pb-2">{{ useAppStore().snackTitle }}</div>

                    <p>{{ useAppStore().snackMessage }}</p>

                    <template v-slot:actions>
                        <v-btn
                            color="white"
                            variant="text"
                            @click="useAppStore().showSnack = false"
                        >
                            Close
                        </v-btn>
                    </template>
                </v-snackbar>
            </v-container>
        </v-main>
    </v-app>

</template>

<style scoped>
a {
    text-decoration: none;
    color: inherit;
}

.router-link-active button {
    background-color: rgba(0, 0, 0, 0.10);
}
</style>