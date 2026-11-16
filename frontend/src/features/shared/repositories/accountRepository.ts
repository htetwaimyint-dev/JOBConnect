import api from "@/services/api";

import type {
    UpdateAccountProfileData,
    UpdateAccountPasswordData,
    AccountResponse,
} from "../types/account.types";

export const accountRepository = {
    updateProfile(data: UpdateAccountProfileData) {
        return api.put<AccountResponse>("/account/profile", data);
    },

    updatePassword(data: UpdateAccountPasswordData) {
        return api.put<AccountResponse>("/account/password", data);
    },
};