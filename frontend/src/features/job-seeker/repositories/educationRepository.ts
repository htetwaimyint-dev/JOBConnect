
import api from "@/services/api";

import type {
    Education,
    CreateEducationData,
    UpdateEducationData,
} from "../types/education.types";

interface EducationResponse {
    success: boolean;
    message: string;
    data: Education;
}

interface EducationListResponse {
    success: boolean;
    message: string;
    data: Education[];
}

export const educationRepository = {
    // Get all education records
    async getAll(): Promise<Education[]> {
        const response = await api.get<EducationListResponse>(
            "/job-seeker/educations",
        );

        return response.data.data;
    },

    // Create education
    async create(data: CreateEducationData): Promise<Education> {
        const response = await api.post<EducationResponse>(
            "/job-seeker/educations",
            data,
        );

        return response.data.data;
    },

    // Update education
    async update(
        id: number,
        data: UpdateEducationData,
    ): Promise<Education> {
        const response = await api.put<EducationResponse>(
            `/job-seeker/educations/${id}`,
            data,
        );

        return response.data.data;
    },

    // Delete education
    async delete(id: number): Promise<void> {
        await api.delete(`/job-seeker/educations/${id}`);
    },
};
