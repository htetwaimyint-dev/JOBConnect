import type { Company, Job } from "../types/company.types";

export const company: Company = {
    id: 1,
    name: "ABC Company Limited",
    slug: "abc-company",

    logo: "https://images.unsplash.com/photo-1602934445884-da0fa1c9d3b3?q=80&w=958&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=NHwxMjA3fDA%3D",

    coverImage:
        "https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=1600",

    tagline: "Building a better future through innovation and people.",

    description: `
    ABC Company Limited is a growing organization committed to delivering innovative solutions.

    We believe in empowering people, encouraging creativity and creating meaningful career opportunities for our employees.
  `,

    industry: "Information Technology",

    location: "Yangon, Myanmar",

    website: "https://example.com",

    foundedYear: 2012,

    employeeCount: "500 - 1,000",

    openJobsCount: 12,

    isVerified: true,

    benefits: [
        {
            id: 1,
            title: "Career Development",
            description: "Training and professional development opportunities.",
            icon: "growth",
        },
        {
            id: 2,
            title: "Friendly Environment",
            description: "Collaborative and supportive workplace culture.",
            icon: "team",
        },
        {
            id: 3,
            title: "Work-Life Balance",
            description: "We value employee wellbeing and personal time.",
            icon: "heart",
        },
        {
            id: 4,
            title: "Career Opportunities",
            description: "Opportunities to grow and advance your career.",
            icon: "career",
        },
    ],

    gallery: [
        "https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=800",
        "https://images.unsplash.com/photo-1497366216548-37526070297c?w=800",
        "https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=800",
    ],
};

export const jobs: Job[] = [
    {
        id: 1,
        title: "Senior Software Engineer",
        department: "Engineering",
        location: "Yangon",
        employmentType: "Full Time",
        experienceLevel: "Experienced",
        postedAt: "2 days ago",
    },
    {
        id: 2,
        title: "UI/UX Designer",
        department: "Design",
        location: "Yangon",
        employmentType: "Full Time",
        experienceLevel: "Mid Level",
        postedAt: "3 days ago",
    },
    {
        id: 3,
        title: "HR Executive",
        department: "Human Resources",
        location: "Yangon",
        employmentType: "Full Time",
        experienceLevel: "Entry Level",
        postedAt: "5 days ago",
    },
];
