import type {
    EmployerOfferJob,
    JobOffer,
    JobOfferCandidate,
    JobOfferCompany,
} from "../types/jobOffer.types";

/**
 * Demo-only fixtures. These values are illustrative and are not loaded from or
 * persisted to the Laravel API.
 */
export const dummyCandidate: JobOfferCandidate = {
    id: 27,
    name: "Aung",
    email: "aung@example.com",
    profile: {
        profile_photo: null,
        phone: "+95 9 765 432 100",
        date_of_birth: "1994-06-18",
        gender: "male",
        address: "Bahan Township",
        city: "Yangon",
        professional_title: "Senior Full-Stack Developer",
        bio: "Full-stack engineer specializing in Laravel APIs and Vue 3 / TypeScript applications. Enjoys designing maintainable systems, improving application performance, and mentoring development teams.",
        expected_salary: 3800,
        experience_years: 6.5,
        linkedin_url: "https://www.linkedin.com/",
        portfolio_url: "https://github.com/",
    },
};

export const dummyCompany: JobOfferCompany = {
    id: 1,
    name: "Nexus Cloud Labs",
    logo: null,
};

export const dummyJobs: EmployerOfferJob[] = [
    {
        id: 104,
        title: "Staff Full-Stack Engineer (Laravel + Vue)",
        employment_type: "full_time",
        workplace_type: "hybrid",
        country: "Myanmar",
        city: "Yangon",
        salary_min: 3500,
        salary_max: 4500,
        salary_type: "fixed",
    },
    {
        id: 109,
        title: "Lead Backend Architect",
        employment_type: "full_time",
        workplace_type: "remote",
        country: "Myanmar",
        city: "Yangon",
        salary_min: 4200,
        salary_max: 5500,
        salary_type: "fixed",
    },
    {
        id: 112,
        title: "Senior Product Engineer",
        employment_type: "contract",
        workplace_type: "remote",
        country: "Myanmar",
        city: null,
        salary_min: 3000,
        salary_max: 3800,
        salary_type: "fixed",
    },
];

function daysFromNow(days: number): string {
    const date = new Date();
    date.setDate(date.getDate() + days);
    return date.toISOString();
}

function daysAgo(days: number): string {
    const date = new Date();
    date.setDate(date.getDate() - days);
    return date.toISOString();
}

function makeOffer(
    overrides: Partial<JobOffer> &
        Pick<JobOffer, "id" | "job_id" | "salary_offered" | "status">,
): JobOffer {
    const job =
        dummyJobs.find((item) => item.id === overrides.job_id) ?? dummyJobs[0];
    const createdAt = overrides.created_at ?? daysAgo(2);

    return {
        id: overrides.id,
        job_id: overrides.job_id,
        job_seeker_id: dummyCandidate.id,
        company_id: dummyCompany.id,
        salary_offered: overrides.salary_offered,
        offer_details: overrides.offer_details ?? null,
        expires_at: overrides.expires_at ?? daysFromNow(10),
        status: overrides.status,
        responded_at: overrides.responded_at ?? null,
        created_at: createdAt,
        updated_at: overrides.updated_at ?? createdAt,
        job: overrides.job ?? job,
        company: overrides.company ?? dummyCompany,
        job_seeker: overrides.job_seeker ?? dummyCandidate,
    };
}

export const dummyOffers: JobOffer[] = [
    makeOffer({
        id: 501,
        job_id: 104,
        salary_offered: 4200,
        status: "pending",
        created_at: daysAgo(1),
        expires_at: daysFromNow(9),
        offer_details:
            "Hi Aung, your experience building Laravel APIs and Vue applications stood out to our engineering team. We would like to invite you to join Nexus Cloud Labs as a Staff Full-Stack Engineer.",
    }),
    makeOffer({
        id: 502,
        job_id: 109,
        salary_offered: 5000,
        status: "pending",
        created_at: daysAgo(3),
        expires_at: daysFromNow(5),
        offer_details:
            "We were impressed by your backend architecture experience. We would be pleased to discuss this Lead Backend Architect opportunity with you.",
    }),
    makeOffer({
        id: 503,
        job_id: 112,
        salary_offered: 3600,
        status: "accepted",
        created_at: daysAgo(8),
        expires_at: daysFromNow(6),
        responded_at: daysAgo(5),
        updated_at: daysAgo(5),
        offer_details:
            "Your product engineering background is a strong match for this role. We are looking forward to working with you.",
    }),
    makeOffer({
        id: 504,
        job_id: 104,
        salary_offered: 3900,
        status: "rejected",
        created_at: daysAgo(14),
        expires_at: daysAgo(2),
        responded_at: daysAgo(11),
        updated_at: daysAgo(11),
        offer_details:
            "We would like to invite you to discuss this full-stack engineering position.",
    }),
    makeOffer({
        id: 505,
        job_id: 109,
        salary_offered: 4700,
        status: "expired",
        created_at: daysAgo(20),
        expires_at: daysAgo(6),
        updated_at: daysAgo(6),
        offer_details:
            "This offer was available for a limited time. Please contact the hiring team if you would like to discuss future opportunities.",
    }),
    makeOffer({
        id: 506,
        job_id: 112,
        salary_offered: 3400,
        status: "withdrawn",
        created_at: daysAgo(30),
        expires_at: daysFromNow(2),
        updated_at: daysAgo(25),
        offer_details:
            "The hiring team initially shared this contract opportunity, but later withdrew the offer.",
    }),
];

export function makeDemoExpiryDate(days = 14): string {
    const date = new Date();
    date.setDate(date.getDate() + days);
    date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
    return date.toISOString().slice(0, 16);
}
