import type { Job } from "@/features/jobs/types/job.types";

export interface AiRecommendation {
    id: number;
    match_score: number | string;
    reason: string | null;
    is_viewed: boolean;
    job: Job;
}

export interface RecommendationResponse {
    analysis: {
        id: number;
        status: string;
        created_at: string;
    } | null;

    recommendations: AiRecommendation[];
    total: number;
    is_refreshing: boolean;
}
