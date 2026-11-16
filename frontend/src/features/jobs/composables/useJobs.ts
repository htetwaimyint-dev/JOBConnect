import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";

import jobRepository from "../repositories/jobRepository";
import { computed, toValue } from "vue";
import type { MaybeRefOrGetter } from "vue";
import { jobKeys } from "./jobKeys";

import type { JobFormData, JobListParams } from "../types/job.types";

export function useJobs() {
    const queryClient = useQueryClient();

    // Get public jobs with search and filters
    function getJobs(params: MaybeRefOrGetter<JobListParams> = {}) {
        const currentParams = computed(() => ({
            page: 1,
            ...toValue(params),
        }));

        return useQuery({
            queryKey: computed(() => jobKeys.list(currentParams.value)),

            queryFn: async () => {
                const response = await jobRepository.getJobs(
                    currentParams.value,
                );

                return response.data;
            },
        });
    }

    // Get job details
    function getJobById(id: number) {
        return useQuery({
            queryKey: jobKeys.detail(id),

            queryFn: async () => {
                const response = await jobRepository.getJobById(id);
                return response.data;
            },

            enabled: id > 0,
        });
    }

    // Create employer job
    const createJob = useMutation({
        mutationFn: async (data: JobFormData) => {
            const response = await jobRepository.createJob(data);
            return response.data;
        },

        onSuccess: () => {
            queryClient.invalidateQueries({
                queryKey: jobKeys.lists(),
            });
        },
    });

    // Update employer job
    function updateJob() {
        const queryClient = useQueryClient();

        return useMutation({
            mutationFn: async ({
                id,
                data,
            }: {
                id: number;
                data: JobFormData;
            }) => {
                const response = await jobRepository.updateJob(id, data);
                return response.data;
            },

            onSuccess: (_, variables) => {
                queryClient.invalidateQueries({
                    queryKey: jobKeys.lists(),
                });

                queryClient.invalidateQueries({
                    queryKey: jobKeys.detail(variables.id),
                });
            },
        });
    }

    // Delete employer job
    function deleteJob() {
        const queryClient = useQueryClient();

        return useMutation({
            mutationFn: async (id: number) => {
                const response = await jobRepository.deleteJob(id);
                return response.data;
            },

            onSuccess: () => {
                queryClient.invalidateQueries({
                    queryKey: jobKeys.lists(),
                });
            },
        });
    }

    return {
        getJobs,
        getJobById,
        createJob,
        updateJob,
        deleteJob,
    };
}
