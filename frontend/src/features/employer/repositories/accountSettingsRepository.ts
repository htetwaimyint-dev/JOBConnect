import api from "@/services/api";

const accountSettingsRepository = {
  // Get employer account information
  getAccount() {
    return api.get("/employer/settings/account");
  },

  // Update employer name and email
  updateAccount(data: {
    name: string;
    email: string;
  }) {
    return api.put("/employer/settings/account", data);
  },

  // Change employer password
  changePassword(data: {
    current_password: string;
    new_password: string;
    new_password_confirmation: string;
  }) {
    return api.put("/employer/settings/password", data);
  },
};

export default accountSettingsRepository;
