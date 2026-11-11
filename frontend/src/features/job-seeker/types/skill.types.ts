
export type SkillLevel =
    | "Beginner"
    | "Intermediate"
    | "Advanced"
    | "Expert";

export interface Skill {
    id: number;
    name: string;
    skill_level: SkillLevel;
    created_at: string | null;
    updated_at: string | null;
}

export interface CreateSkillData {
    name: string;
    skill_level: SkillLevel;
}

export interface UpdateSkillData {
    name?: string;
    skill_level?: SkillLevel;
}
