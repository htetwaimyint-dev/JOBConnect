import { useQuery, useMutation } from "@tanstack/vue-query";
import recommendationRepository from "../repositories/recommendationRepository";
import type { RecommendationResponse } from "../types/recommendation.types";

export function useRecommendations() {
    const getRecommendations = (limit = 4) => {
        return useQuery({
            queryKey: ["recommendations", limit],

            queryFn: async (): Promise<RecommendationResponse> => {
                const response =
                    await recommendationRepository.getRecommendations(limit);

                return response.data.data;
            },

            refetchInterval: (query) =>
                query.state.data?.is_refreshing ? 5000 : false,
        });
    };

    const generateRecommendations = useMutation({
        mutationFn: async (
            limit: number = 10,
        ): Promise<RecommendationResponse> => {
            const response =
                await recommendationRepository.generateRecommendations(limit);
            return response.data.data;
        },
    });

    return {
        getRecommendations,
        generateRecommendations,
    };
}
