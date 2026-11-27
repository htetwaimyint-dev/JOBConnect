import { useQuery } from "@tanstack/vue-query";
import { dashboardRepository } from "../repositories/dashboardRepository";

export function useEmployerApplicantStats() {
  return useQuery({
    queryKey: ["employer-applicant-stats"],
    queryFn: () => dashboardRepository.getApplicantStats(),
  });
}
