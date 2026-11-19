import { computed, toValue } from "vue";
import type { MaybeRefOrGetter } from "vue";
import { useMutation, useQuery, useQueryClient } from "@tanstack/vue-query";
import jobOfferRepository from "../repositories/jobOfferRepository";
import type { JobOfferResponsePayload } from "../types/jobOffer.types";

export const jobOfferKeys = {
    all: ["job-offers"] as const,
    employerContext: (candidateId: number | string) =>
        [...jobOfferKeys.all, "employer-context", String(candidateId)] as const,
    seekerInbox: () => [...jobOfferKeys.all, "seeker-inbox"] as const,
};

export function useJobOffers() {
    const queryClient = useQueryClient();

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
        mutationFn: async (
            payload: Parameters<typeof jobOfferRepository.create>[0],
        ) => {
            const response = await jobOfferRepository.create(payload);
            return response.data.data;
        },
        onSuccess: async () => {
            await Promise.all([
                queryClient.invalidateQueries({
                    queryKey: jobOfferKeys.seekerInbox(),
                }),
                queryClient.invalidateQueries({ queryKey: jobOfferKeys.all }),
            ]);
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

    const withdrawJobOffer = useMutation({
        mutationFn: async (offerId: number) => {
            const response = await jobOfferRepository.withdraw(offerId);

            return response.data.data;
        },

        onSuccess: async () => {
            await queryClient.invalidateQueries({
                queryKey: jobOfferKeys.all,
            });
        },
    });

    return {
        getEmployerContext,
        getMyOffers,
        withdrawJobOffer,
        createJobOffer,
        respondToJobOffer,
    };
}
