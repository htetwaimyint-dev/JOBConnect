export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

export interface DateRange {
  from: string;
  to: string;
}

export interface ApplicantStats {
  total: number;
  pending: number;
  reviewed: number;
  shortlisted: number;
  interview: number;
  accepted: number;
  rejected: number;
  withdrawn: number;
  date_range: DateRange;
}

export interface OfferStats {
  total: number;
  pending: number;
  accepted: number;
  rejected: number;
  expired: number;
  withdrawn: number;
  date_range: DateRange;
}

export interface Interviewer {
  id: number;
  name: string;
  position: string;
}

export interface DashboardInterview {
  id: number;
  application_id: number;
  interview_type: "online" | "onsite";
  scheduled_at: string;
  location: string | null;
  meeting_link: string | null;
  status: string;

  application: {
    id: number;
    applicant_name: string;
    job: {
      id: number;
      title: string;
    };
  };

  interviewers: Interviewer[];
}

export interface InterviewDashboard {
  today_interviews: DashboardInterview[];
  upcoming_interviews: DashboardInterview[];
}
