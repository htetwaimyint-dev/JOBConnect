export interface JobSeekerSkill {
  id: number;
  name: string;
  skill_level: string | null;
}

export interface JobSeekerEducation {
  id: number;
  institution: string;
  degree: string | null;
  field_of_study: string | null;
  start_date: string | null;
  end_date: string | null;
  description: string | null;
}

export interface JobSeeker {
  id: number;
  name: string;
  profile_photo: string | null;
  professional_title: string | null;
  city: string | null;
  bio: string | null;
  phone: string | null;
  gender: string | null;
  address: string | null;
  expected_salary: number | null;
  experience_years: number | null;
  linkedin_url: string | null;
  portfolio_url: string | null;
  skills: JobSeekerSkill[];
  educations: JobSeekerEducation[];
}

export interface JobSeekerFilters {
  search: string;
  per_page: number;
}
