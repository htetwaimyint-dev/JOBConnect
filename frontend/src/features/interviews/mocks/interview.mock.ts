import type {
    Interview,
    InterviewApplication,
    Interviewer,
} from "../types/interview.types";

export const mockInterviewers: Interviewer[] = [
    {
        id: 1,
        name: "James Wilson",
        position: "HR Manager",
    },
    {
        id: 2,
        name: "Sarah Johnson",
        position: "Technical Lead",
    },
    {
        id: 3,
        name: "Michael Brown",
        position: "Engineering Manager",
    },
    {
        id: 4,
        name: "Emily Davis",
        position: "Product Manager",
    },
];

export const mockApplications: InterviewApplication[] = [
    {
        id: 101,
        applicant_name: "Alex Morgan",
        applicant_phone: "+1 555 0101",
        status: "shortlisted",
        applied_at: "2026-11-15T09:00:00",
        job: {
            id: 1,
            title: "Senior Frontend Developer",
            company: {
                id: 1,
                name: "Tech Solutions",
                logo: null,
            },
        },
    },
    {
        id: 102,
        applicant_name: "Sophia Lee",
        applicant_phone: "+1 555 0102",
        status: "shortlisted",
        applied_at: "2026-11-16T10:00:00",
        job: {
            id: 2,
            title: "UI/UX Designer",
            company: {
                id: 1,
                name: "Tech Solutions",
                logo: null,
            },
        },
    },
    {
        id: 103,
        applicant_name: "Daniel Smith",
        applicant_phone: "+1 555 0103",
        status: "shortlisted",
        applied_at: "2026-11-17T11:00:00",
        job: {
            id: 3,
            title: "Backend Developer",
            company: {
                id: 1,
                name: "Tech Solutions",
                logo: null,
            },
        },
    },
    {
        id: 104,
        applicant_name: "Olivia Taylor",
        applicant_phone: "+1 555 0104",
        status: "shortlisted",
        applied_at: "2026-11-18T08:00:00",
        job: {
            id: 4,
            title: "Product Designer",
            company: {
                id: 1,
                name: "Tech Solutions",
                logo: null,
            },
        },
    },
];

export const mockInterviews: Interview[] = [
    {
        id: 1,
        application_id: 101,
        interview_type: "online",
        scheduled_at: "2026-11-20T09:00:00",
        location: null,
        meeting_link: "https://meet.example.com/interview-1",
        notes: "Discuss frontend architecture and Vue experience.",
        status: "scheduled",
        interviewers: [mockInterviewers[0]!, mockInterviewers[1]!],
        application: mockApplications[0],
        created_at: "2026-11-18T09:00:00",
        updated_at: "2026-11-18T09:00:00",
    },
    {
        id: 2,
        application_id: 102,
        interview_type: "onsite",
        scheduled_at: "2026-11-20T11:00:00",
        location: "Meeting Room A",
        meeting_link: null,
        notes: "Portfolio review and design discussion.",
        status: "scheduled",
        interviewers: [mockInterviewers[0]!, mockInterviewers[3]!],
        application: mockApplications[1],
        created_at: "2026-11-18T10:00:00",
        updated_at: "2026-11-18T10:00:00",
    },
    {
        id: 3,
        application_id: 103,
        interview_type: "phone",
        scheduled_at: "2026-11-21T14:00:00",
        location: null,
        meeting_link: null,
        notes: "Initial technical screening.",
        status: "scheduled",
        interviewers: [mockInterviewers[2]!],
        application: mockApplications[2],
        created_at: "2026-11-18T11:00:00",
        updated_at: "2026-11-18T11:00:00",
    },
];
