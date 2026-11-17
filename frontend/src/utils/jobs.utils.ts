import type {
    Job,
    JobFormData,
    SubmissionStatus,
} from "@/features/jobs/types/job.types";
import DOMPurify from "dompurify";
import { reactive } from "vue";

export function sanitizeHtml(content?: string | null): string {
    if (!content) return "";

    return DOMPurify.sanitize(content);
}

export function formatJobLabel(value?: string | null): string {
    if (!value) return "";

    const labels: Record<string, string> = {
        full_time: "Full Time",
        part_time: "Part Time",
        contract: "Contract",
        internship: "Internship",
        onsite: "On-site",
        remote: "Remote",
        hybrid: "Hybrid",
        any: "Any Gender",
        male: "Male",
        female: "Female",
    };

    return labels[value] ?? value;
}

export function formatJobSalary(job: {
    salary_type: string;
    salary_min?: number | string | null;
    salary_max?: number | string | null;
}): string {
    if (job.salary_type === "negotiable") {
        return "Negotiable";
    }

    if (job.salary_min == null || job.salary_max == null) {
        return "Not specified";
    }

    const formatter = new Intl.NumberFormat("en-US");

    return `${formatter.format(Number(job.salary_min))} - ${formatter.format(Number(job.salary_max))}`;
}

export const form = reactive<JobFormData>({
    title: "",
    category_id: null,
    deadline: "",
    employment_type: "full_time",
    vacancies: 1,
    gender: "any",
    workplace_type: "onsite",

    country: "",
    city: "",

    salary_type: "fixed",
    salary_min: undefined,
    salary_max: undefined,

    description: "",
    requirements: "",
    responsibilities: "",

    submission_status: "pending",
    job_questions: [],
});

export function populateForm(job: Job) {
    Object.assign(form, {
        title: job.title,
        category_id: job.category?.id ?? null,
        deadline: job.deadline ?? "",
        employment_type: job.employment_type,
        vacancies: job.vacancies,
        gender: job.gender,
        workplace_type: job.workplace_type,

        country: job.country ?? "",
        city: job.city ?? "",

        salary_type: job.salary_type,
        salary_min: job.salary_min ?? undefined,
        salary_max: job.salary_max ?? undefined,

        description: job.description,
        requirements: job.requirements,
        responsibilities: job.responsibilities ?? "",

        submission_status: (job.status === "draft"
            ? "draft"
            : "pending") as SubmissionStatus,

        job_questions: (job.job_questions ?? []).map((item) => ({
            id: item.id,
            question: item.question,
            is_required: Boolean(item.is_required),
            sort_order: item.sort_order,
        })),
    });
}
