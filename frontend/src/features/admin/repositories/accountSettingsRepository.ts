import api from "@/services/api";

const accountSettingsRepository = {
  // Get admin account information
  getAccount() {
    return api.get("/admin/settings/account");
  },

  // Update admin name and email
  updateAccount(data: {
    name: string;
    email: string;
  }) {
    return api.put("/admin/settings/account", data);
  },

  // Change admin password
  changePassword(data: {
    current_password: string;
    new_password: string;
    new_password_confirmation: string;
  }) {
    return api.put("/admin/settings/password", data);
  },
};

export default accountSettingsRepository;
