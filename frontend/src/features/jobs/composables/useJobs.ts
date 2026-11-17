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
        const response = await jobRepository.getJobs(currentParams.value);

        return response.data;
      },
    });
  }

  function getJobsByCompany(params: MaybeRefOrGetter<JobListParams> = {}) {
    const currentParams = computed(() => ({
      page: 1,
      ...toValue(params),
    }));

    return useQuery({
      queryKey: computed(() => jobKeys.list(currentParams.value)),

      queryFn: async () => {
        const response = await jobRepository.getJobsByCompany(
          currentParams.value,
        );

        return response.data;
      },
    });
  }

  // Get job details
  function getJobById(id: MaybeRefOrGetter<number | string>) {
    // 1. Always unwrap the reactive value cleanly first
    const jobId = computed(() => toValue(id));

    return useQuery({
      queryKey: computed(() => jobKeys.detail(jobId.value)),

      queryFn: async () => {
        const response = await jobRepository.getJobById(jobId.value);
        return response.data.data;
      },
      enabled: computed(
        () => Boolean(jobId.value) && jobId.value !== "undefined",
      ),
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

    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({
        queryKey: jobKeys.lists(),
      });

      queryClient.invalidateQueries({
        queryKey: jobKeys.detail(variables.id),
      });
    },
  });

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
    getJobsByCompany,
    getJobById,
    createJob,
    updateJob,
    deleteJob,
  };
}
