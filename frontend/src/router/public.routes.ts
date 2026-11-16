import type { RouteRecordRaw } from "vue-router";

import PublicLayout from "@/layouts/PublicLayout.vue";
import HomePage from "@/features/jobs/pages/HomePage.vue";
import JobListPage from "@/features/jobs/pages/JobListPage.vue";
import JobDetailPage from "@/features/jobs/pages/JobDetailPage.vue";

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
        path: "jobs/:id",
        name: "PublicJobDetail",
        component: JobDetailPage,
        props: true,
      },
    ],
  },
];
