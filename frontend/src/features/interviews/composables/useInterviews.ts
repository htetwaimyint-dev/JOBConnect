import { computed, toValue } from "vue";
import type { MaybeRefOrGetter } from "vue";
import { useMutation, useQuery, useQueryClient } from "@tanstack/vue-query";

import interviewRepository from "../repositories/interviewRepository";
import { interviewKeys } from "./interviewKeys";
import type {
    InterviewFilters,
    InterviewFormData,
    InterviewStatus,
    InterviewerFormData,
    JobseekerInterviewFilters,
} from "../types/interview.types";

const defaultFilters: InterviewFilters = {
    search: "",
    status: "all",
    interview_type: "all",
    from: "",
    to: "",
};

export function useInterviews() {
    const queryClient = useQueryClient();

    function getInterviews(
        filters: MaybeRefOrGetter<InterviewFilters> = defaultFilters,
    ) {
        const currentFilters = computed(() => ({
            ...defaultFilters,
            ...toValue(filters),
        }));

        return useQuery({
            queryKey: computed(() => interviewKeys.list(currentFilters.value)),
            queryFn: () =>
                interviewRepository.getInterviews(currentFilters.value),
        });
    }

    function getInterview(id: MaybeRefOrGetter<number>) {
        const currentId = computed(() => toValue(id));

        return useQuery({
            queryKey: computed(() => interviewKeys.detail(currentId.value)),
            queryFn: () => interviewRepository.getInterview(currentId.value),
            enabled: computed(() => currentId.value > 0),
        });
    }

    function getInterviewers() {
        return useQuery({
            queryKey: interviewKeys.interviewers(),
            queryFn: () => interviewRepository.getInterviewers(),
        });
    }

    function getAvailableApplications() {
        return useQuery({
            queryKey: interviewKeys.applications(),
            queryFn: () => interviewRepository.getAvailableApplications(),
        });
    }

    function getJobseekerInterviews(
        filters: MaybeRefOrGetter<JobseekerInterviewFilters> = defaultFilters,
    ) {
        const currentFilters = computed(() => ({
            ...defaultFilters,
            ...toValue(filters),
        }));

        return useQuery({
            queryKey: computed(() => [
                ...interviewKeys.all,
                "jobseeker",
                currentFilters.value,
            ]),
            queryFn: () =>
                interviewRepository.getJobseekerInterviews(
                    currentFilters.value,
                ),
        });
    }

    const refreshInterviewData = () =>
        queryClient.invalidateQueries({ queryKey: interviewKeys.all });

    const createInterview = useMutation({
        mutationFn: (data: InterviewFormData) =>
            interviewRepository.createInterview(data),
        onSuccess: refreshInterviewData,
    });

    const updateInterview = useMutation({
        mutationFn: ({
            id,
            data,
        }: {
            id: number;
            data: Partial<InterviewFormData>;
        }) => interviewRepository.updateInterview(id, data),
        onSuccess: (interview) => {
            queryClient.setQueryData(
                interviewKeys.detail(interview.id),
                interview,
            );
            return refreshInterviewData();
        },
    });

    const updateInterviewStatus = useMutation({
        mutationFn: ({ id, status }: { id: number; status: InterviewStatus }) =>
            interviewRepository.updateInterviewStatus(id, status),
        onSuccess: (interview) => {
            queryClient.setQueryData(
                interviewKeys.detail(interview.id),
                interview,
            );
            return refreshInterviewData();
        },
    });

    const requestReschedule = useMutation({
        mutationFn: ({
            id,
            data,
        }: {
            id: number;
            data: { scheduled_at?: string | null; reason?: string | null };
        }) => interviewRepository.requestReschedule(id, data),
        onSuccess: (interview) => {
            queryClient.setQueryData(
                interviewKeys.detail(interview.id),
                interview,
            );
            return refreshInterviewData();
        },
    });

    const createInterviewer = useMutation({
        mutationFn: (data: InterviewerFormData) =>
            interviewRepository.createInterviewer(data),
        onSuccess: refreshInterviewData,
    });

    const updateInterviewer = useMutation({
        mutationFn: ({ id, data }: { id: number; data: InterviewerFormData }) =>
            interviewRepository.updateInterviewer(id, data),
        onSuccess: refreshInterviewData,
    });

    const deleteInterviewer = useMutation({
        mutationFn: (id: number) => interviewRepository.deleteInterviewer(id),
        onSuccess: refreshInterviewData,
    });

    return {
        getInterviews,
        getInterview,
        getInterviewers,
        getAvailableApplications,
        getJobseekerInterviews,
        createInterview,
        updateInterview,
        updateInterviewStatus,
        createInterviewer,
        updateInterviewer,
        deleteInterviewer,
        requestReschedule,
    };
}
