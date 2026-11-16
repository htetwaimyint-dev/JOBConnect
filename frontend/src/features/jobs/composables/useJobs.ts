import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";

import jobRepository from "../repositories/jobRepository";
import { computed, toValue } from "vue";
import { jobKeys } from "./jobKeys";

import type { JobFormData, JobListParams } from "../types/job.types";
import type { MaybeRefOrGetter } from "vue";

export function useJobs() {
    function getJobs(params?: JobListParams) {
        return useQuery({
            queryKey: computed(() => jobKeys.list(params?.page ?? 1)),
            queryFn: async () => {
                const response = await jobRepository.getJobs(params);
                return response.data;
            },
        });
    }

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

    const queryClient = useQueryClient();

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
