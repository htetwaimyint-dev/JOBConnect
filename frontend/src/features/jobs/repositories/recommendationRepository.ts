import api from "@/services/api";

const recommendationRepository = {
    getRecommendations(limit = 4) {
        return api.get("/job-seeker/recommendations", {
            params: { limit },
        });
    },
    generateRecommendations(limit = 10) {
        return api.post("/job-seeker/recommendations", {
            limit,
        });
    },
};

export default recommendationRepository;
