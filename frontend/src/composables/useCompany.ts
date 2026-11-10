import { useQuery } from "@tanstack/vue-query";
import { companyService } from "../services/company.service";

export function useCompanyProfile() {
    return useQuery({
        queryKey: ["company", "profile"],

        queryFn: () => companyService.getCompanyProfile(),

        staleTime: 1000 * 60 * 5,

        retry: 1,
    });
}
