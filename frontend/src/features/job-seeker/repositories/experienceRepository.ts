import api from "@/services/api";

import type {
    Experience,
    CreateExperienceData,
    UpdateExperienceData,
} from "../types/experience.types";

interface ExperienceResponse {
    success: boolean;
    message: string;
    data: Experience;
}

interface ExperienceListResponse {
    success: boolean;
    message: string;
    data: Experience[];
}

export const experienceRepository = {
    // Get all experience records
    async getAll(): Promise<Experience[]> {
        const response = await api.get<ExperienceListResponse>(
            "/job-seeker/experiences",
        );

        return response.data.data;
    },

    // Create experience
    async create(data: CreateExperienceData): Promise<Experience> {
        const response = await api.post<ExperienceResponse>(
            "/job-seeker/experiences",
            data,
        );

        return response.data.data;
    },

    // Update experience
    async update(
        id: number,
        data: UpdateExperienceData,
    ): Promise<Experience> {
        const response = await api.put<ExperienceResponse>(
            `/job-seeker/experiences/${id}`,
            data,
        );

        return response.data.data;
    },

    // Delete experience
    async delete(id: number): Promise<void> {
        await api.delete(`/job-seeker/experiences/${id}`);
    },
};