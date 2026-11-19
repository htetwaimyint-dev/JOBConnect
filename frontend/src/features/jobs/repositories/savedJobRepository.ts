
import api from "@/services/api";

const savedJobRepository = {
    // Get saved jobs
    getSavedJobs(params?: { page?: number }) {
        return api.get("/job-seeker/saved-jobs", {
            params,
        });
    },

    // Save a job
    saveJob(jobId: number) {
        return api.post("/job-seeker/saved-jobs", {
            job_id: jobId,
        });
    },

    // Unsave a job
    removeSavedJob(jobId: number) {
        return api.delete(`/job-seeker/saved-jobs/${jobId}`);
    },
};

export default savedJobRepository;