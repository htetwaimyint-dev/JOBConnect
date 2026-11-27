
import api from "@/services/api";

import type {
    JobSeekerProfile,
    UpdateProfileData,
} from "../types/profile.types";

interface ProfileResponse {
    success: boolean;
    message: string;
    data: JobSeekerProfile | null;
}

export const profileRepository = {
    // Get logged-in Job Seeker Profile
    async getProfile(): Promise<JobSeekerProfile | null> {
        try {
            const response = await api.get<ProfileResponse>(
                "/job-seeker/profile",
            );

            return response.data.data;
        } catch (error) {
            const status = (
                error as { response?: { status?: number } }
            ).response?.status;

            if (status === 404) {
                return null;
            }

            throw error;
        }
    },

    // Get another Job Seeker Profile by ID (Employer)
    async getProfileById(id: number): Promise<JobSeekerProfile> {
        const response = await api.get<ProfileResponse>(
            `/employer/job-seekers/${id}/profile`,
        );

        if (!response.data.data) {
            throw new Error("Profile not found.");
        }

        return response.data.data;
    },

    // Create or Update logged-in Job Seeker Profile
    async updateProfile(
        data: UpdateProfileData,
    ): Promise<JobSeekerProfile> {
        const formData = new FormData();

        Object.entries(data).forEach(([key, value]) => {
            if (value !== undefined && value !== null && value !== "") {
                formData.append(
                    key,
                    value instanceof File ? value : String(value),
                );
            } else if (value === null) {
                formData.append(key, "");
            }
        });

        formData.append("_method", "PUT");

        const response = await api.post<ProfileResponse>(
            "/job-seeker/profile",
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            },
        );

        if (!response.data.data) {
            throw new Error("Failed to save profile.");
        }

        return response.data.data;
    },
};
