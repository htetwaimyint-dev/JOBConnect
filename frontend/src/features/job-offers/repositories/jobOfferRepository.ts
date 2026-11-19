import api from "@/services/api";
import type {
    CreateJobOfferPayload,
    EmployerOfferJob,
    JobOffer,
    JobOfferCandidate,
    JobOfferCompany,
    JobOfferResponsePayload,
} from "../types/jobOffer.types";

export interface ApiResponse<T> {
    success: boolean;
    message: string;
    data: T;
}

export interface EmployerJobOfferContext {
    candidate: JobOfferCandidate;
    company: JobOfferCompany;
    jobs: EmployerOfferJob[];
}

export interface SendJobOfferPayload extends CreateJobOfferPayload {
    job_seeker_id: number;
}

const jobOfferRepository = {
    getEmployerContext(jobSeekerId: number | string) {
        return api.get<ApiResponse<EmployerJobOfferContext>>(
            `employer/job-offers/context/${jobSeekerId}`,
        );
    },

    create(payload: SendJobOfferPayload) {
        return api.post<ApiResponse<JobOffer>>("employer/job-offers", payload);
    },

    getMyOffers() {
        return api.get<ApiResponse<JobOffer[]>>("job-seeker/job-offers");
    },

    respond(offerId: number, payload: JobOfferResponsePayload) {
        return api.patch<ApiResponse<JobOffer>>(
            `job-seeker/job-offers/${offerId}/response`,
            payload,
        );
    },

    withdraw(offerId: number) {
        return api.delete(`/job-offers/${offerId}/withdraw`);
    },
};

export default jobOfferRepository;
