import { useQuery } from "@tanstack/vue-query";
import { dashboardRepository } from "../repositories/dashboardRepository";

export function useEmployerOfferStats() {
  return useQuery({
    queryKey: ["employer-offer-stats"],
    queryFn: () => dashboardRepository.getOfferStats(),
  });
}
