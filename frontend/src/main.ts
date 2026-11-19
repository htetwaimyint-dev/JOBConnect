import { createApp } from "vue";
import { createPinia } from "pinia";
import { VueQueryPlugin } from "@tanstack/vue-query";

import App from "./App.vue";
import "./assets/styles/main.css";
import '@fontsource-variable/inter';
import router from "./router/index.ts";
import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const app = createApp(App);

app.use(createPinia());

app.use(VueQueryPlugin, {
    queryClientConfig: {
        defaultOptions: {
            queries: {
                staleTime: 1000 * 60 * 5,
                retry: 1,
                refetchOnWindowFocus: false,
            },
        },
    },
});

app.use(Vue3Toastify, {
    autoClose: 3000,
    position: "top-right",
    theme: "light",
    pauseOnHover: true,
    closeOnClick: true,
    transition: "slide",
});

app.use(router);

app.mount("#app");
