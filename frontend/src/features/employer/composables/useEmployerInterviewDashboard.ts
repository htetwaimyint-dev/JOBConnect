import { useQuery } from "@tanstack/vue-query";
import { dashboardRepository } from "../repositories/dashboardRepository";

export function useEmployerInterviewDashboard() {
  return useQuery({
    queryKey: ["employer-interview-dashboard"],
    queryFn: () => dashboardRepository.getInterviewDashboard(),
  });
}
