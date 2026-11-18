
import { computed, ref } from "vue";

import applicationRepository from "../repositories/applicationRepository";

import type {
    Application,
    ApplicationPagination,
    ApplicationStatus,
} from "../types/application.types";

export function useApplications() {
    // Application State
    const applications = ref<Application[]>([]);
    const loading = ref(false);
    const errorMessage = ref("");

    // Search and Filter
    const searchKeyword = ref("");
    const selectedStatus = ref<ApplicationStatus | "">("");

    // Pagination
    const currentPage = ref(1);

    const pagination = ref<ApplicationPagination>({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
    });

    // Fetch Applications
    const fetchApplications = async (page = 1) => {
        loading.value = true;
        errorMessage.value = "";

        try {
            const response =
                await applicationRepository.getMyApplications({
                    page,
                    per_page: 10,
                    status: selectedStatus.value || undefined,
                });

            applications.value = response.data;
            pagination.value = response.meta;
            currentPage.value = response.meta.current_page;
        } catch (error: unknown) {
            const apiError = error as {
                response?: {
                    data?: {
                        message?: string;
                    };
                };
            };

            errorMessage.value =
                apiError.response?.data?.message ??
                "Failed to load your applications. Please try again.";
        } finally {
            loading.value = false;
        }
    };

    // Search Applications
    const filteredApplications = computed(() => {
        const keyword = searchKeyword.value.trim().toLowerCase();

        if (!keyword) {
            return applications.value;
        }

        return applications.value.filter((application) => {
            return (
                application.job.title.toLowerCase().includes(keyword) ||
                application.job.company.name.toLowerCase().includes(keyword) ||
                (application.resume?.title ?? "")
                    .toLowerCase()
                    .includes(keyword)
            );
        });
    });

    // Application Statistics
    const pageStats = computed(() => ({
        pending: applications.value.filter(
            (application) => application.status === "pending",
        ).length,

        interviews: applications.value.filter(
            (application) => application.status === "interview",
        ).length,

        accepted: applications.value.filter(
            (application) => application.status === "accepted",
        ).length,
    }));


    // Withdraw Application
    const withdrawApplication = async (applicationId: number) => {
        const confirmed = window.confirm(
            "Are you sure you want to withdraw this application?",
        );

        if (!confirmed) {
            return;
        }

        try {
            await applicationRepository.withdrawApplication(applicationId);

            // Refresh current application list
            await fetchApplications(currentPage.value);

        } catch (error: unknown) {
            const apiError = error as {
                response?: {
                    data?: {
                        message?: string;
                    };
                };
            };

            window.alert(
                apiError.response?.data?.message ??
                "Failed to withdraw application. Please try again.",
            );
        }
    };

    // Change Status
    const changeStatus = () => {
        searchKeyword.value = "";
        fetchApplications(1);
    };

    // Pagination
    const changePage = (page: number) => {
        if (
            page < 1 ||
            page > pagination.value.last_page ||
            loading.value
        ) {
            return;
        }

        fetchApplications(page);
    };

    // Date Formatting
    const formatDate = (date: string) => {
        if (!date) return "Date unavailable";

        const parsedDate = new Date(date);

        if (Number.isNaN(parsedDate.getTime())) {
            return "Date unavailable";
        }

        return parsedDate.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    };

    return {
        applications,
        loading,
        errorMessage,
        currentPage,
        selectedStatus,
        searchKeyword,
        pagination,
        filteredApplications,
        pageStats,
        fetchApplications,
        changeStatus,
        changePage,
        formatDate,
        withdrawApplication,
    };
}
