import type { Employer, JobSeeker } from "../types/user.types";

const employers: Employer[] = [
  {
    id: 1,
    name: "TechCorp Solutions",
    email: "contact@techcorp.com",
    role: "Employer",
    logo: "https://images.unsplash.com/photo-1549923746-c502d488b3ea?w=100&h=100&fit=crop",
    company: "TechCorp Solutions",
    companyLogo:
      "https://images.unsplash.com/photo-1549923746-c502d488b3ea?w=100&h=100&fit=crop",
    jobCount: 12,
    isDeleted: false,
    joinedDate: "Mar 12, 2024",
  },
  {
    id: 2,
    name: "Creative Studio",
    email: "hello@creativestudio.com",
    role: "Employer",
    logo: "https://images.unsplash.com/photo-1572044162444-ad60f128bdea?w=100&h=100&fit=crop",
    company: "Creative Studio",
    companyLogo:
      "https://images.unsplash.com/photo-1572044162444-ad60f128bdea?w=100&h=100&fit=crop",
    jobCount: 5,
    isDeleted: false,
    joinedDate: "Mar 10, 2024",
  },
  {
    id: 3,
    name: "Global Finance",
    email: "hr@globalfinance.com",
    role: "Employer",
    logo: "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=100&h=100&fit=crop",
    company: "Global Finance",
    companyLogo:
      "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=100&h=100&fit=crop",
    jobCount: 0,
    isDeleted: true,
    joinedDate: "Feb 28, 2024",
  },
  {
    id: 4,
    name: "Startup Inc.",
    email: "jobs@startupinc.com",
    role: "Employer",
    logo: "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&h=100&fit=crop",
    company: "Startup Inc.",
    companyLogo:
      "https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&h=100&fit=crop",
    jobCount: 3,
    isDeleted: false,
    joinedDate: "Feb 15, 2024",
  },
  {
    id: 5,
    name: "NextGen Digital",
    email: "hr@nextgen.com",
    role: "Employer",
    logo: "https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=100&h=100&fit=crop",
    company: "NextGen Digital",
    companyLogo:
      "https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=100&h=100&fit=crop",
    jobCount: 8,
    isDeleted: false,
    joinedDate: "Feb 02, 2024",
  },
];

const jobSeekers: JobSeeker[] = [
  {
    id: 101,
    name: "Alex Morgan",
    email: "alex.morgan@example.com",
    role: "Job Seeker",
    logo: "https://images.unsplash.com/photo-1500649468467-7b1f7a2a4a?auto=format&fit=crop&w=100&h=100",
    profileTitle: "Frontend Developer",
    isDeleted: false,
    joinedDate: "Mar 12, 2024",
  },
  {
    id: 102,
    name: "Sophia Lee",
    email: "sophia.lee@example.com",
    role: "Job Seeker",
    logo: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop",
    profileTitle: "UI/UX Designer",
    isDeleted: false,
    joinedDate: "Mar 10, 2024",
  },
  {
    id: 103,
    name: "Daniel Kim",
    email: "daniel.kim@example.com",
    role: "Job Seeker",
    logo: "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&h=100&fit=crop",
    profileTitle: "Backend Developer",
    isDeleted: true,
    joinedDate: "Feb 28, 2024",
  },
  {
    id: 104,
    name: "Olivia Chen",
    email: "olivia.chen@example.com",
    role: "Job Seeker",
    logo: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop",
    profileTitle: "Product Designer",
    isDeleted: false,
    joinedDate: "Feb 15, 2024",
  },
  {
    id: 105,
    name: "Noah Williams",
    email: "noah.williams@example.com",
    role: "Job Seeker",
    logo: "https://images.unsplash.com/photo-1507591064344-4c6ce005b128?w=100&h=100&fit=crop",
    profileTitle: "Software Engineer",
    isDeleted: false,
    joinedDate: "Feb 02, 2024",
  },
];

// Mock repositories. Replace these methods with Axios calls when connecting Laravel.
const userRepository = {
  async getEmployers(): Promise<Employer[]> {
    return employers.map((user) => ({ ...user }));
  },
  async getJobSeekers(): Promise<JobSeeker[]> {
    return jobSeekers.map((user) => ({ ...user }));
  },
  async toggleEmployerStatus(id: number): Promise<void> {
    const user = employers.find((item) => item.id === id);
    if (user) user.isDeleted = !user.isDeleted;
  },
  async toggleJobSeekerStatus(id: number): Promise<void> {
    const user = jobSeekers.find((item) => item.id === id);
    if (user) user.isDeleted = !user.isDeleted;
  },
};

export default userRepository;
