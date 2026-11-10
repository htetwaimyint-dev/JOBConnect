
import { createRouter, createWebHistory } from "vue-router";

// Demo Components
import Button from "@/components/common/demo/Button.vue";
import Checkbox from "@/components/common/demo/Checkbox.vue";
import Input from "@/components/common/demo/Input.vue";
import PasswordInput from "@/components/common/demo/PasswordInput.vue";
import Select from "@/components/common/demo/Select.vue";
import Alert from "@/components/common/demo/Alert.vue";

// Layouts
import PublicLayout from "@/layouts/PublicLayout.vue";
import JobSeekerLayout from "@/layouts/JobSeekerLayout.vue";
import EmployeeLayout from "@/layouts/EmployerLayout.vue";

const routes = [
    // Public Website Layout
    {
        path: "/",
        component: PublicLayout,
        children: [
            // Add your public website pages here later
        ],
    },

    // Job Seeker Layout
    {
        path: "/jobseeker",
        component: JobSeekerLayout,
        children: [
            {
                path: "dashboard",
                name: "JobSeekerDashboard",
                component: () =>
                    import("@/features/job-seeker/pages/Dashboard.vue"),
            },
        ],
    },

    // Employee Layout
    {
        path: "/employer",
        component: EmployeeLayout,
        children: [
            {
                path: "dashboard",
                name: "EmployerDashboard",
                component: () =>
                    import("@/features/employer/pages/Dashboard.vue"),
            },
        ],
    },

    // Demo Routes
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
    routes,
});

export default router;
