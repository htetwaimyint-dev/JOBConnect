import type { Job } from "./job.types";

export interface AiRecommendation {
    id: number;
    match_score: number | string;
    reason: string | null;
    is_viewed: boolean;
    job: Job;
}

export interface RecommendationAnalysis {
    id: number;
    status: string;
    created_at: string;
}

export interface RecommendationResponse {
    analysis: RecommendationAnalysis | null;
    recommendations: AiRecommendation[];
    total: number;
    is_refreshing: boolean;
}
