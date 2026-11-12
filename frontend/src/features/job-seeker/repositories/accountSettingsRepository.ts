import api from "@/services/api";

const accountSettingsRepository = {
  // Get current job seeker profile
  getProfile() {
    return api.get("/job-seeker/profile");
  },

  // Update name, email and profile photo
  updateAccount(formData: FormData) {

    formData.append("_method", "PUT");
    
    return api.post("/job-seeker/settings/account", formData, {
    
    });
  },

  // Change password
  changePassword(data: {
    current_password: string;
    new_password: string;
    new_password_confirmation: string;
  }) {
    return api.put("/job-seeker/settings/password", data);
  },
};

export default accountSettingsRepository;
