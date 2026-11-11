import api from "@/services/api";

const accountSettingsRepository = {
  // Get current job seeker profile
  getProfile() {
    return api.get("/job-seeker/profile");
  },

  // Update name, email and profile photo
  updateAccount(formData: FormData) {
    return api.post("/job-seeker/settings/account", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
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
