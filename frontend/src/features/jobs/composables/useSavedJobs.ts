import { computed } from "vue";
import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";

import savedJobRepository from "../repositories/savedJobRepository";
import { useAuthStore } from "@/stores/auth.store";

export function useSavedJobs() {
  const queryClient = useQueryClient();
  const authStore = useAuthStore();

  const isJobSeeker = computed(() => authStore.user?.role === "job_seeker");

  // Query key
  const savedJobKey = ["saved-jobs"];

  // Get saved jobs
  const savedJobsQuery = useQuery({
    queryKey: savedJobKey,

    queryFn: async () => {
      const response = await savedJobRepository.getSavedJobs();

      return response.data;
    },

    enabled: isJobSeeker,
  });

  // Saved job IDs
  const savedJobIds = computed<number[]>(() => {
    const records = savedJobsQuery.data.value?.data ?? [];

    return records.map((item: any) => item.job.id);
  });

  // Check whether a job is saved
  function isSaved(jobId: number) {
    return computed(() => savedJobIds.value.includes(jobId));
  }

  // Save job
  const saveMutation = useMutation({
    mutationFn: (jobId: number) => savedJobRepository.saveJob(jobId),

    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: savedJobKey,
      });
    },
  });

  // Unsave job
  const removeMutation = useMutation({
    mutationFn: (jobId: number) => savedJobRepository.removeSavedJob(jobId),

    onSuccess: () => {
      queryClient.invalidateQueries({
        queryKey: savedJobKey,
      });
    },
  });

  // Toggle save / unsave
  async function toggleSavedJob(jobId: number) {
    if (!isJobSeeker.value) {
      return;
    }

    if (savedJobIds.value.includes(jobId)) {
      await removeMutation.mutateAsync(jobId);
    } else {
      await saveMutation.mutateAsync(jobId);
    }
  }

  return {
    savedJobsQuery,
    savedJobIds,
    isSaved,
    toggleSavedJob,
    saveMutation,
    removeMutation,
    isJobSeeker,
  };
}
