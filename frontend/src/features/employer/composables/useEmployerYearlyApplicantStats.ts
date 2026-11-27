import { computed, type Ref } from "vue";
import { useQuery } from "@tanstack/vue-query";
import { dashboardRepository } from "../repositories/dashboardRepository";

export const useEmployerYearlyApplicantStats = (year: Ref<number>) => {
  return useQuery({
    queryKey: computed(() => [
      "employer",
      "yearly-applicant-stats",
      year.value,
    ]),

    queryFn: () => dashboardRepository.getYearlyApplicantStats(year.value),

    staleTime: 5 * 60 * 1000,
  });
};
