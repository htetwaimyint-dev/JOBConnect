import type { Job } from "@/features/jobs/types/job.types";

export function formatJobSalary(
  job: Pick<Job, "salary_min" | "salary_max" | "salary_type">,
): string {
  if (
    job.salary_type === "negotiable" ||
    (!job.salary_min && !job.salary_max)
  ) {
    return "Negotiable";
  }

  const formatNum = (val: string | number | null) => {
    if (!val) return "";
    return Number(val).toLocaleString();
  };

  if (job.salary_min && job.salary_max) {
    return `${formatNum(job.salary_min)} - ${formatNum(job.salary_max)} MMK`;
  }

  return `${formatNum(job.salary_min || job.salary_max)} MMK`;
}
