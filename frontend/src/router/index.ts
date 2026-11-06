import { createRouter, createWebHistory } from "vue-router";
import Button from "@/components/common/demo/Button.vue";
import Checkbox from "@/components/common/demo/Checkbox.vue";
import Input from "@/components/common/demo/Input.vue";
import PasswordInput from "@/components/common/demo/PasswordInput.vue";
import Select from "@/components/common/demo/Select.vue";
import Alert from "@/components/common/demo/Alert.vue";

const routes = [
    {
        path: "/button",
        component: Button,
    },
    {
        path: "/checkbox",
        component: Checkbox,
    },
    {
        path: "/input",
        component: Input,
    },
    {
        path: "/passwordinput",
        component: PasswordInput,
    },
    {
        path: "/select",
        component: Select,
    },
    {
        path: "/alert",
        component: Alert,
    },
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: routes,
});

export default router;
