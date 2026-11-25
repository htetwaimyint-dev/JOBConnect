import type { InterviewFilters } from "../types/interview.types";

export const interviewKeys = {
  all: ["interviews"] as const,
  lists: () => [...interviewKeys.all, "list"] as const,
  list: (filters: InterviewFilters) =>
    [...interviewKeys.lists(), filters] as const,
  detail: (id: number) => [...interviewKeys.all, "detail", id] as const,
  interviewers: () => [...interviewKeys.all, "interviewers"] as const,
  applications: () => [...interviewKeys.all, "applications"] as const,
};
