import { useQuery } from "@tanstack/vue-query";
import { companyRepository } from "../repositories/companyRepository";

export const useEmployerJobStats = () => {
  return useQuery({
    queryKey: ["employer", "job-stats"],

    queryFn: () => companyRepository.getJobStats(),

    staleTime: 1000 * 60,
  });
};
