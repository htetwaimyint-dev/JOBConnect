import type { RouteRecordRaw } from "vue-router";

import PublicLayout from "@/layouts/PublicLayout.vue";
import HomePage from "@/features/jobs/pages/HomePage.vue";
import JobListPage from "@/features/jobs/pages/JobListPage.vue";
import JobDetail from "@/features/jobs/pages/JobDetail.vue";

export const publicRoutes: RouteRecordRaw[] = [
    {
        path: "/",
        component: PublicLayout,
        children: [
            {
                path: "",
                name: "Home",
                component: HomePage,
            },
            {
                path: "jobs",
                name: "PublicJobs",
                component: JobListPage,
            },
            {
                path: "/jobs/:id",
                name: "job-detail",
                component: JobDetail,
            },
        ],
    },
];
