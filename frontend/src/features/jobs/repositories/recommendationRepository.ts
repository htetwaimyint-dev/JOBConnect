import api from "@/services/api";

const recommendationRepository = {
    getRecommendations(limit = 10) {
        return api.get("/job-seeker/recommendations", {
            params: { limit },
        });
    },
};

export default recommendationRepository;
