
import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";

import jobRepository from "../repositories/jobRepository";
import { computed, toValue } from "vue";
import type { MaybeRefOrGetter } from "vue";
import { jobKeys } from "./jobKeys";

import type { JobFormData, JobListParams } from "../types/job.types";
import { useAuthStore } from "@/stores/auth.store";

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

    // Get employer's own jobs with search and filters
    function getJobsByCompany(params: MaybeRefOrGetter<JobListParams> = {}) {
        const currentParams = computed(() => ({
            page: 1,
            ...toValue(params),
        }));

        return useQuery({
            queryKey: computed(() =>
                jobKeys.employerList(currentParams.value),
            ),

            queryFn: async () => {
                const response = await jobRepository.getJobsByCompany(
                    currentParams.value,
                );

                return response.data;
            },
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
    const updateJob = useMutation({
        mutationFn: async ({ id, data }: { id: number; data: JobFormData }) => {
            const response = await jobRepository.updateJob(id, data);
            return response.data;
        },

        onSuccess: async (_, variables) => {
            await Promise.all([
                queryClient.invalidateQueries({
                    queryKey: jobKeys.lists(),
                }),

                queryClient.invalidateQueries({
                    queryKey: jobKeys.employerDetail(variables.id),
                }),
            ]);
        },
    });


    // Close published employer job
    const closeJob = useMutation({
        mutationFn: async (id: number) => {
            const response = await jobRepository.closeJob(id);
            return response.data;
        },

        onSuccess: (_, id) => {
            // Refresh employer job lists
            queryClient.invalidateQueries({
                queryKey: jobKeys.lists(),
            });

            // Refresh specific job detail
            queryClient.invalidateQueries({
                queryKey: jobKeys.detail(id),
            });
        },
    });

    // Delete employer job
    const deleteJob = useMutation({
        mutationFn: async (id: number) => {
            const response = await jobRepository.deleteJob(id);
            return response.data;
        },

        onSuccess: (_, id) => {
            // Refresh employer job lists
            queryClient.invalidateQueries({
                queryKey: jobKeys.lists(),
            });

            // Refresh specific job detail
            queryClient.invalidateQueries({
                queryKey: jobKeys.detail(id),
            });
        },
    });

    // Get job details
    function getJobById(id: MaybeRefOrGetter<number | string>) {
        const jobId = computed(() => toValue(id));
        const authStore = useAuthStore();

        const isEmployer = computed(() => authStore.user?.role === "employer");

        return useQuery({
            queryKey: computed(() =>
                isEmployer.value
                    ? jobKeys.employerDetail(jobId.value)
                    : jobKeys.publicDetail(jobId.value),
            ),

            queryFn: async () => {
                const response = isEmployer.value
                    ? await jobRepository.getEmployerJobById(jobId.value)
                    : await jobRepository.getPublicJobById(jobId.value);

                return response.data.data;
            },

            enabled: computed(() => Boolean(jobId.value)),
        });
    }

    return {
        getJobs,
        getJobsByCompany,
        getJobById,
        createJob,
        updateJob,
        closeJob,
        deleteJob,
    };
}