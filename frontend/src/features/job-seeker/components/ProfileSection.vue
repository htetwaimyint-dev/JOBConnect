<script setup lang="ts">
import { onMounted, ref } from "vue";
import {
    UserRound,
    Pencil,
    Phone,
    CalendarDays,
    MapPin,
    VenusAndMars,
    BriefcaseBusiness,
    FileText,
    Wallet,
    Clock3,
    Linkedin,
    Globe,
    LoaderCircle,
    AlertCircle,
    CheckCircle2,
} from "lucide-vue-next";

import { useUserStore } from "@/stores/user.store";
import { profileRepository } from "../repositories/profileRepository";

import ProfileForm from "./ProfileForm.vue";

import type {
    JobSeekerProfile,
    UpdateProfileData,
} from "../types/profile.types";

const userStore = useUserStore();

const profile = ref<JobSeekerProfile | null>(null);

const showForm = ref(false);
const isLoading = ref(false);
const isSaving = ref(false);

const errorMessage = ref("");
const successMessage = ref("");

// Load profile
const loadProfile = async () => {
    isLoading.value = true;
    errorMessage.value = "";

    try {
        profile.value = await profileRepository.getProfile();

        userStore.setProfilePhoto(profile.value.profile_photo);
    } catch (error) {
        console.error("Failed to load profile:", error);
        errorMessage.value = "Failed to load profile information.";
    } finally {
        isLoading.value = false;
    }
};

// Open edit modal
const openEditForm = () => {
    errorMessage.value = "";
    successMessage.value = "";
    showForm.value = true;
};

// Close modal
const closeForm = () => {
    if (isSaving.value) return;

    showForm.value = false;
};

// Save profile
const saveProfile = async (data: UpdateProfileData) => {
    errorMessage.value = "";
    successMessage.value = "";

    isSaving.value = true;

    try {
        profile.value = await profileRepository.updateProfile(data);

        userStore.setProfilePhoto(profile.value.profile_photo);

        showForm.value = false;
        successMessage.value = "Your profile has been updated successfully.";
    } catch (error) {
        console.error("Failed to update profile:", error);

        errorMessage.value =
            "Failed to update profile. Please check your inputs and try again.";
    } finally {
        isSaving.value = false;
    }
};

// Format date
const formatDate = (date: string | null) => {
    if (!date) return "Not provided";

    return new Date(date).toLocaleDateString();
};

// Format salary
const formatSalary = (salary: number | null) => {
    if (salary === null) return "Not provided";

    return new Intl.NumberFormat().format(salary);
};

onMounted(loadProfile);
</script>

<template>
    <section class="space-y-5">

        <!-- Error Message -->
        <div v-if="errorMessage"
            class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <AlertCircle :size="19" class="mt-0.5 shrink-0" />
            <p class="flex-1">{{ errorMessage }}</p>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage"
            class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
            <CheckCircle2 :size="19" class="mt-0.5 shrink-0" />
            <p class="flex-1">{{ successMessage }}</p>
            <button type="button" aria-label="Dismiss success" @click="successMessage = ''">
                ×
            </button>
        </div>

        <!-- Loading -->
        <div v-if="isLoading" class="rounded-2xl border border-slate-200 bg-white py-16 text-center shadow-sm">
            <LoaderCircle :size="32" class="mx-auto animate-spin text-blue-600" />

            <p class="mt-3 text-sm font-medium text-slate-500">
                Loading your profile...
            </p>
        </div>

        <!-- Profile Content -->
        <div v-else-if="profile" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <!-- Profile Header -->
            <div
                class="flex flex-col gap-5 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 sm:flex-row sm:items-center">
                <div
                    class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-4 border-white bg-blue-100 text-blue-600 shadow-sm">
                    <img v-if="profile.profile_photo" :src="profile.profile_photo" alt="Profile photo"
                        class="h-full w-full object-cover" />

                    <UserRound v-else :size="40" />
                </div>

                <div class="min-w-0 flex-1">
                    <h3 class="text-xl font-bold text-slate-800">
                        {{ profile.professional_title || "Job Seeker" }}
                    </h3>

                    <p class="mt-1 text-sm font-medium text-blue-700">
                        {{ profile.professional_title || "Professional Title Not Set" }}
                    </p>

                    <p class="mt-2 text-sm text-slate-500">
                        {{ profile.city || "City not provided" }}
                    </p>
                </div>

                <button type="button"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    @click="openEditForm">
                    <Pencil :size="16" />
                    Edit Profile
                </button>
            </div>

            <div class="space-y-6 p-5 sm:p-6">

                <!-- Personal Information -->
                <div>
                    <div class="mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <UserRound :size="19" class="text-blue-600" />
                        <h3 class="font-bold text-slate-800">
                            Personal Information
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div class="profile-detail">
                            <Phone :size="17" />
                            <div>
                                <p class="detail-label">Phone Number</p>
                                <p class="detail-value">
                                    {{ profile.phone || "Not provided" }}
                                </p>
                            </div>
                        </div>

                        <div class="profile-detail">
                            <CalendarDays :size="17" />
                            <div>
                                <p class="detail-label">Date of Birth</p>
                                <p class="detail-value">
                                    {{ formatDate(profile.date_of_birth) }}
                                </p>
                            </div>
                        </div>

                        <div class="profile-detail">
                            <VenusAndMars :size="17" />
                            <div>
                                <p class="detail-label">Gender</p>
                                <p class="detail-value capitalize">
                                    {{ profile.gender || "Not provided" }}
                                </p>
                            </div>
                        </div>

                        <div class="profile-detail">
                            <MapPin :size="17" />
                            <div>
                                <p class="detail-label">City</p>
                                <p class="detail-value">
                                    {{ profile.city || "Not provided" }}
                                </p>
                            </div>
                        </div>

                        <div class="profile-detail sm:col-span-2">
                            <MapPin :size="17" />
                            <div>
                                <p class="detail-label">Full Address</p>
                                <p class="detail-value">
                                    {{ profile.address || "Not provided" }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div>
                    <div class="mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <BriefcaseBusiness :size="19" class="text-indigo-600" />
                        <h3 class="font-bold text-slate-800">
                            Professional Information
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div class="profile-detail sm:col-span-2">
                            <BriefcaseBusiness :size="17" />
                            <div>
                                <p class="detail-label">Professional Title</p>
                                <p class="detail-value">
                                    {{ profile.professional_title || "Not provided" }}
                                </p>
                            </div>
                        </div>

                        <div class="profile-detail sm:col-span-2">
                            <FileText :size="17" />
                            <div>
                                <p class="detail-label">Professional Bio</p>
                                <p class="detail-value whitespace-pre-line">
                                    {{ profile.bio || "Not provided" }}
                                </p>
                            </div>
                        </div>

                        <div class="profile-detail">
                            <Wallet :size="17" />
                            <div>
                                <p class="detail-label">Expected Salary</p>
                                <p class="detail-value">
                                    {{ formatSalary(profile.expected_salary) }}
                                </p>
                            </div>
                        </div>

                        <div class="profile-detail">
                            <Clock3 :size="17" />
                            <div>
                                <p class="detail-label">Experience Years</p>
                                <p class="detail-value">
                                    {{ profile.experience_years ?? "Not provided" }}
                                    <span v-if="profile.experience_years !== null">years</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Links -->
                <div>
                    <div class="mb-4 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <Globe :size="19" class="text-violet-600" />
                        <h3 class="font-bold text-slate-800">
                            Social & Portfolio Links
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div class="profile-detail">
                            <Linkedin :size="17" />
                            <div class="min-w-0">
                                <p class="detail-label">LinkedIn</p>
                                <a v-if="profile.linkedin_url" :href="profile.linkedin_url" target="_blank"
                                    rel="noopener noreferrer"
                                    class="break-all text-sm font-medium text-blue-600 hover:underline">
                                    {{ profile.linkedin_url }}
                                </a>
                                <p v-else class="detail-value">
                                    Not provided
                                </p>
                            </div>
                        </div>

                        <div class="profile-detail">
                            <Globe :size="17" />
                            <div class="min-w-0">
                                <p class="detail-label">Portfolio</p>
                                <a v-if="profile.portfolio_url" :href="profile.portfolio_url" target="_blank"
                                    rel="noopener noreferrer"
                                    class="break-all text-sm font-medium text-blue-600 hover:underline">
                                    {{ profile.portfolio_url }}
                                </a>
                                <p v-else class="detail-value">
                                    Not provided
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- No Profile -->
        <div v-else class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
            <UserRound :size="35" class="mx-auto text-slate-400" />

            <p class="mt-3 font-semibold text-slate-700">
                Profile information is unavailable.
            </p>

            <button type="button"
                class="mt-4 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                @click="loadProfile">
                Try Again
            </button>
        </div>
    </section>

    <!-- Edit Profile Modal -->
    <ProfileForm :show="showForm" :profile="profile" :is-saving="isSaving" @close="closeForm" @save="saveProfile" />
</template>

<style scoped>
.profile-detail {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    min-width: 0;
    color: #64748b;
}

.profile-detail>svg {
    flex-shrink: 0;
    margin-top: 0.15rem;
    color: #64748b;
}

.detail-label {
    margin-bottom: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    color: #94a3b8;
}

.detail-value {
    overflow-wrap: anywhere;
    font-size: 0.875rem;
    font-weight: 600;
    color: #334155;
}
</style>
