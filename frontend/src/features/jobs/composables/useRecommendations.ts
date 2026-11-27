import { useQuery } from "@tanstack/vue-query";
import recommendationRepository from "../repositories/recommendationRepository";
import type { RecommendationResponse } from "../types/recommendation.types";

export function useRecommendations() {
    function getRecommendations(limit = 10) {
        return useQuery({
            queryKey: ["recommendations", limit],

            queryFn: async (): Promise<RecommendationResponse> => {
                const response =
                    await recommendationRepository.getRecommendations(limit);

                return response.data.data;
            },

            // Poll only while the backend is generating new recommendations.
            refetchInterval: (query) =>
                query.state.data?.is_refreshing ? 5000 : false,
        });
    }

    return {
        getRecommendations,
    };
}
