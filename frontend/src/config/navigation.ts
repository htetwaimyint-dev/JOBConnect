import type { Component } from "vue";
import { Settings } from "lucide-vue-next";

import {
  LayoutDashboard,
  Search,
  BriefcaseBusiness,
  Bookmark,
  FileText,
  Send,
  CalendarDays,
  Award,
  UserRound,
  Users,
} from "lucide-vue-next";

export interface NavigationItem {
  label: string;
  to: string;
  icon: Component;
}

// Job Seeker Navigation
export const jobSeekerNavigation: NavigationItem[] = [
  {
    label: "Dashboard",
    to: "/job-seeker/dashboard",
    icon: LayoutDashboard,
  },
  {
    label: "Find Jobs",
    to: "/job-seeker/jobs",
    icon: Search,
  },
  {
    label: "My Applications",
    to: "/job-seeker/applications",
    icon: Send,
  },
  {
    label: "Saved Jobs",
    to: "/job-seeker/saved-jobs",
    icon: Bookmark,
  },
  {
    label: "My Resume",
    to: "/job-seeker/resume",
    icon: FileText,
  },
  {
    label: "Interviews",
    to: "/job-seeker/interviews",
    icon: CalendarDays,
  },
  {
    label: "Certificates",
    to: "/job-seeker/certificates",
    icon: Award,
  },
  {
    label: "My Profile",
    to: "/job-seeker/profile",
    icon: UserRound,
  },
  {
    label: "Settings",
    to: "/jobseeker/settings",
    icon: Settings,
  },
];

// Employee Navigation
export const employeeNavigation: NavigationItem[] = [
  {
    label: "Dashboard",
    to: "/employer/dashboard",
    icon: LayoutDashboard,
  },
  {
    label: "Manage Jobs",
    to: "/employer/jobs",
    icon: BriefcaseBusiness,
  },
  {
    label: "Applications",
    to: "/employer/applications",
    icon: FileText,
  },
  {
    label: "Interviews",
    to: "/employer/interviews",
    icon: CalendarDays,
  },
  {
    label: "Candidates",
    to: "/employer/candidates",
    icon: Users,
  },
  {
    label: "My Profile",
    to: "/employer/profile",
    icon: UserRound,
  },
];