import { computed, toValue } from "vue";
import type { MaybeRefOrGetter } from "vue";
import { useMutation, useQuery, useQueryClient } from "@tanstack/vue-query";
import jobOfferRepository from "../repositories/jobOfferRepository";
import type {
    CreateJobOfferPayload,
    JobOfferResponsePayload,
} from "../types/jobOffer.types";

export const jobOfferKeys = {
    all: ["job-offers"] as const,
    employerList: () => [...jobOfferKeys.all, "employer-list"] as const,
    employerContext: (candidateId: number | string) =>
        [...jobOfferKeys.all, "employer-context", String(candidateId)] as const,
    seekerInbox: () => [...jobOfferKeys.all, "seeker-inbox"] as const,
};

export function useJobOffers() {
    const queryClient = useQueryClient();

    function getEmployerOffers() {
        return useQuery({
            queryKey: jobOfferKeys.employerList(),
            queryFn: async () => {
                const response = await jobOfferRepository.getEmployerOffers();
                return response.data.data;
            },
        });
    }

    function getEmployerContext(
        candidateId: MaybeRefOrGetter<number | string>,
    ) {
        const id = computed(() => toValue(candidateId));

        return useQuery({
            queryKey: computed(() => jobOfferKeys.employerContext(id.value)),
            queryFn: async () => {
                const response = await jobOfferRepository.getEmployerContext(
                    id.value,
                );
                return response.data.data;
            },
            enabled: computed(() => Number(id.value) > 0),
        });
    }

    function getMyOffers() {
        return useQuery({
            queryKey: jobOfferKeys.seekerInbox(),
            queryFn: async () => {
                const response = await jobOfferRepository.getMyOffers();
                return response.data.data;
            },
        });
    }

    const createJobOffer = useMutation({
        mutationFn: async (payload: CreateJobOfferPayload) => {
            const response = await jobOfferRepository.create(payload);
            return response.data.data;
        },
        onSuccess: async (_data, variables) => {
            const contextKey = jobOfferKeys.employerContext(
                Number(variables.job_seeker_id),
            );

            // 1. Remove the offered job from the dropdown immediately
            queryClient.setQueryData(contextKey, (oldData: any) => {
                if (!oldData) return oldData;

                return {
                    ...oldData,
                    jobs: (oldData.jobs ?? []).filter(
                        (job: { id: number }) =>
                            Number(job.id) !== Number(variables.job_id),
                    ),
                };
            });

            // 2. Keep all relevant queries synced in the background
            await Promise.all([
                queryClient.invalidateQueries({
                    queryKey: contextKey,
                }),
                queryClient.invalidateQueries({
                    queryKey: jobOfferKeys.employerList(),
                }),
                queryClient.invalidateQueries({
                    queryKey: jobOfferKeys.seekerInbox(),
                }),
            ]);
        },
    });

    const withdrawJobOffer = useMutation({
        mutationFn: async (offerId: number) => {
            const response = await jobOfferRepository.withdraw(offerId);
            return response.data.data;
        },
        onSuccess: async () => {
            await queryClient.invalidateQueries({
                queryKey: jobOfferKeys.employerList(),
            });
        },
    });

    const respondToJobOffer = useMutation({
        mutationFn: async ({
            offerId,
            payload,
        }: {
            offerId: number;
            payload: JobOfferResponsePayload;
        }) => {
            const response = await jobOfferRepository.respond(offerId, payload);
            return response.data.data;
        },
        onSuccess: async () => {
            await queryClient.invalidateQueries({
                queryKey: jobOfferKeys.seekerInbox(),
            });
        },
    });

    return {
        getEmployerOffers,
        getEmployerContext,
        getMyOffers,
        createJobOffer,
        withdrawJobOffer,
        respondToJobOffer,
    };
}
