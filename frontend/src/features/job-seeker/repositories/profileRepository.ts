import api from "@/services/api";

import type {
    JobSeekerProfile,
    UpdateProfileData,
} from "../types/profile.types";

interface ProfileResponse {
    success: boolean;
    message: string
    data: JobSeekerProfile;
}

export const profileRepository = {
    // Get logged-in Job Seeker Profile
    async getProfile(): Promise<JobSeekerProfile> {
        const response = await api.get<ProfileResponse>(
            "/job-seeker/profile",
        );

        return response.data.data;
    },

    // Get another Job Seeker Profile by ID (Employer)
    async getProfileById(id: number): Promise<JobSeekerProfile> {
        const response = await api.get<ProfileResponse>(
            `/employer/job-seekers/${id}/profile`,
        );

        return response.data.data;
    },

    // Update logged-in Job Seeker Profile
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

        return response.data.data;
    },
};
