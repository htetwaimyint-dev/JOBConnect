
import api from "@/services/api";

import type {
    Skill,
    CreateSkillData,
    UpdateSkillData,
} from "../types/skill.types";

interface SkillResponse {
    success: boolean;
    message: string;
    data: Skill;
}

interface SkillListResponse {
    success: boolean;
    message: string;
    data: Skill[];
}

export const skillsRepository = {
    // Get all skills
    async getAll(): Promise<Skill[]> {
        const response = await api.get<SkillListResponse>(
            "/job-seeker/skills",
        );

        return response.data.data;
    },

    // Create skill
    async create(data: CreateSkillData): Promise<Skill> {
        const response = await api.post<SkillResponse>(
            "/job-seeker/skills",
            data,
        );

        return response.data.data;
    },

    // Update skill
    async update(
        id: number,
        data: UpdateSkillData,
    ): Promise<Skill> {
        const response = await api.put<SkillResponse>(
            `/job-seeker/skills/${id}`,
            data,
        );

        return response.data.data;
    },

    // Delete skill
    async delete(id: number): Promise<void> {
        await api.delete(`/job-seeker/skills/${id}`);
    },
};
