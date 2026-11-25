import type { Component } from "vue";

import {
    LayoutDashboard,
    Search,
    FileText,
    Bookmark,
    CalendarDays,
    Gift,
    UserRound,
    Settings,
    BriefcaseBusiness,
    Users,
    Building2,
    ClipboardCheck,
    Tags,
    ChartBar,
    UserSearch,
    Send,
} from "lucide-vue-next";

export interface NavigationItem {
    label: string;
    to: string;
    icon: Component;
    children?: NavigationItem[];
}

export interface RoleNavigation {
    main: NavigationItem[];
    account: NavigationItem[];
}

export const navigationConfig: Record<string, RoleNavigation> = {
    // Job Seeker Navigation
    job_seeker: {
        main: [
            {
                label: "Dashboard",
                to: "/jobseeker/dashboard",
                icon: LayoutDashboard,
            },
            {
                label: "Find Jobs",
                to: "/jobseeker/jobs",
                icon: Search,
            },
            {
                label: "My Applications",
                to: "/jobseeker/applications",
                icon: FileText,
            },
            {
                label: "Saved Jobs",
                to: "/jobseeker/saved-jobs",
                icon: Bookmark,
            },
            {
                label: "Interviews",
                to: "/jobseeker/interviews",
                icon: CalendarDays,
            },
            {
                label: "Company Profiles",
                to: "/jobseeker/companyProfile",
                icon: Building2,
            },
            {
                label: "Job Offers",
                to: "/jobseeker/job-offers",
                icon: Gift,
            },
        ],
        account: [
            {
                label: "My Profile",
                to: "/jobseeker/profile",
                icon: UserRound,
            },
            {
                label: "Settings",
                to: "/jobseeker/settings",
                icon: Settings,
            },
        ],
    },

    // Employer Navigation
    employer: {
        main: [
            {
                label: "Dashboard",
                to: "/employer/dashboard",
                icon: LayoutDashboard,
            },
            {
                label: "Job Posts",
                to: "/employer/jobs",
                icon: BriefcaseBusiness,
            },
            {
                label: "Applicants",
                to: "/employer/applicants",
                icon: Users,
            },
            {
                label: "Company Profile",
                to: "/employer/profile",
                icon: Building2,
            },
            {
                label: "Job Seeker Profiles",
                to: "/employer/job-seekers",
                icon: UserSearch,
            },
            {
                label: "Direct Offers",
                to: "/employer/job-offers",
                icon: Send,
            },
        ],
        account: [
            {
                label: "Settings",
                to: "/employer/settings",
                icon: Settings,
            },
        ],
    },

    // Admin Navigation
    admin: {
        main: [
            {
                label: "Dashboard",
                to: "/admin/dashboard",
                icon: LayoutDashboard,
            },
            {
                label: "User",
                to: "/admin/companies",
                icon: Users,
                children: [
                    {
                        label: "Companies",
                        to: "/admin/companies",
                        icon: Building2,
                    },
                    {
                        label: "Job Seekers",
                        to: "/admin/job-seekers",
                        icon: UserSearch,
                    },
                ],
            },
            {
                label: "Company Verify",
                to: "/admin/company-approvals",
                icon: Building2,
            },
            {
                label: "Job Post Approval",
                to: "/admin/job-approvals",
                icon: ClipboardCheck,
            },
            {
                label: "Category",
                to: "/admin/categories",
                icon: Tags,
            },
            {
                label: "Report",
                to: "/admin/reports",
                icon: ChartBar,
            },
        ],
        account: [
            {
                label: "Settings",
                to: "/admin/settings",
                icon: Settings,
            },
        ],
    },
};
