import api from "@/services/api";

import type {
    Resume,
    CreateResumeData,
    UpdateResumeData,
} from "../types/resume.types";

interface ResumeResponse {
    success: boolean;
    message: string;
    data: Resume;
}

interface ResumeListResponse {
    success: boolean;
    message: string;
    data: Resume[];
}

export const resumeRepository = {
    // Get all resumes
    async getAll(): Promise<Resume[]> {
        const response = await api.get<ResumeListResponse>(
            "/job-seeker/resumes",
        );

        return response.data.data;
    },

    // Upload a new resume
    async create(data: CreateResumeData): Promise<Resume> {
        const formData = new FormData();

        formData.append("title", data.title);
        formData.append("file", data.file);
        formData.append("is_default", data.is_default ? "1" : "0");
        formData.append("visibility", data.visibility ?? "private");

        const response = await api.post<ResumeResponse>(
            "/job-seeker/resumes",
            formData,
        );

        return response.data.data;
    },

    // Update an existing resume
    async update(
        id: number,
        data: UpdateResumeData,
    ): Promise<Resume> {
        const formData = new FormData();

        if (data.title !== undefined) {
            formData.append("title", data.title);
        }

        if (data.file !== undefined) {
            formData.append("file", data.file);
        }

        if (data.is_default !== undefined)
            formData.append("is_default", data.is_default ? "1" : "0");

        if (data.visibility !== undefined) {
            formData.append("visibility", data.visibility);
        }

        // Laravel PUT method spoofing
        formData.append("_method", "PUT");

        const response = await api.post<ResumeResponse>(
            `/job-seeker/resumes/${id}`,
            formData,
        );

        return response.data.data;
    },

    // Download a resume
    async download(id: number): Promise<Blob> {
        const response = await api.get(
            `/job-seeker/resumes/${id}/download`,
            {
                responseType: "blob",
            },
        );

        return response.data;
    },

    // Delete a resume
    async delete(id: number): Promise<void> {
        await api.delete(`/job-seeker/resumes/${id}`);
    },
};
