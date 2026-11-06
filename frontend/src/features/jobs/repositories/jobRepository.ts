import api from '@/services/api'

import type {
    Job,
    JobListParams,
    JobListResponse,
} from '../types/job.types'

const jobRepository = {
    async getJobs(params?: JobListParams): Promise<JobListResponse> {
        const response = await api.get<JobListResponse>('/jobs', {
            params,
        })

        return response.data
    },

    async getJobById(id: number): Promise<{ data: Job }> {
        const response = await api.get<{ data: Job }>(`/jobs/${id}`)

        return response.data
    },
}

export default jobRepository