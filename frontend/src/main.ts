import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "./assets/styles/main.css";
import router from "./router/index.ts";

const app = createApp(App);

app.use(createPinia());
app.use(router);

app.mount("#app");
