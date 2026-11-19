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

const jobOfferRepository = {
    getEmployerOffers() {
        return api.get<ApiResponse<JobOffer[]>>("employer/job-offers");
    },

    getEmployerContext(jobSeekerId: number | string) {
        return api.get<ApiResponse<EmployerJobOfferContext>>(
            `employer/job-offers/context/${jobSeekerId}`,
        );
    },

    create(payload: CreateJobOfferPayload) {
        return api.post<ApiResponse<JobOffer>>("employer/job-offers", payload);
    },

    withdraw(offerId: number) {
        return api.delete<ApiResponse<JobOffer>>(
            `employer/job-offers/${offerId}/withdraw`,
        );
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
};

export default jobOfferRepository;
