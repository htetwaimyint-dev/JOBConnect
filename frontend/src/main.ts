
import { createApp } from "vue";
import { createPinia } from "pinia";
import { VueQueryPlugin } from "@tanstack/vue-query";

import App from "./App.vue";
import "./assets/styles/main.css";
import router from "./router/index.ts";

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

app.use(router);

app.mount("#app");
