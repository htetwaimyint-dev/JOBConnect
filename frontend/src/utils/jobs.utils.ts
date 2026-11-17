import DOMPurify from "dompurify";

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
