<script setup lang="ts">
import { onMounted, onBeforeUnmount, ref } from "vue";
import { useUserStore } from "@/stores/user.store";
import { profileRepository } from "../repositories/profileRepository";

import type {
    JobSeekerProfile,
    UpdateProfileData,
} from "../types/profile.types";

import {
    UserRound,
    Camera,
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
    Save,
    LoaderCircle,
    CheckCircle2,
    AlertCircle,
    Sparkles,
    Upload,
    X,
} from "lucide-vue-next";

const profile = ref<JobSeekerProfile | null>(null);
const userStore = useUserStore();

const loading = ref(false);
const saving = ref(false);

const errorMessage = ref("");
const successMessage = ref("");

const selectedPhoto = ref<File | null>(null);
const photoPreview = ref<string | null>(null);
const photoInput = ref<HTMLInputElement | null>(null);
const temporaryPhotoUrl = ref<string | null>(null);

const form = ref<UpdateProfileData>({
    phone: "",
    date_of_birth: "",
    gender: "",
    address: "",
    city: "",
    professional_title: "",
    bio: "",
    expected_salary: null,
    experience_years: null,
    linkedin_url: "",
    portfolio_url: "",
});

const loadProfile = async () => {
    loading.value = true;
    errorMessage.value = "";

    try {
        profile.value = await profileRepository.getProfile();

        form.value = {
            phone: profile.value.phone,
            date_of_birth: profile.value.date_of_birth,
            gender: profile.value.gender,
            address: profile.value.address,
            city: profile.value.city,
            professional_title: profile.value.professional_title,
            bio: profile.value.bio,
            expected_salary: profile.value.expected_salary,
            experience_years: profile.value.experience_years,
            linkedin_url: profile.value.linkedin_url,
            portfolio_url: profile.value.portfolio_url,
        };

        photoPreview.value = profile.value.profile_photo;
        userStore.setProfilePhoto(profile.value.profile_photo);
    } catch (error) {
        errorMessage.value = "Failed to load profile information.";
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const handlePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (!file) return;

    const allowedTypes = [
        "image/jpeg",
        "image/png",
        "image/webp",
    ];

    if (!allowedTypes.includes(file.type)) {
        errorMessage.value = "Please select a JPG, PNG or WEBP image.";
        target.value = "";
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        errorMessage.value = "Profile photo must not exceed 2MB.";
        target.value = "";
        return;
    }

    errorMessage.value = "";
    successMessage.value = "";

    if (temporaryPhotoUrl.value) {
        URL.revokeObjectURL(temporaryPhotoUrl.value);
    }

    selectedPhoto.value = file;
    temporaryPhotoUrl.value = URL.createObjectURL(file);
    photoPreview.value = temporaryPhotoUrl.value;
};

const removeSelectedPhoto = () => {
    selectedPhoto.value = null;

    if (temporaryPhotoUrl.value) {
        URL.revokeObjectURL(temporaryPhotoUrl.value);
        temporaryPhotoUrl.value = null;
    }

    photoPreview.value = profile.value?.profile_photo ?? null;

    if (photoInput.value) {
        photoInput.value.value = "";
    }
};

const saveProfile = async () => {
    saving.value = true;
    errorMessage.value = "";
    successMessage.value = "";

    try {
        const updateData: UpdateProfileData = {
            ...form.value,
            profile_photo: selectedPhoto.value,
        };

        profile.value = await profileRepository.updateProfile(updateData);

        selectedPhoto.value = null;

        if (temporaryPhotoUrl.value) {
            URL.revokeObjectURL(temporaryPhotoUrl.value);
            temporaryPhotoUrl.value = null;
        }

        photoPreview.value = profile.value.profile_photo;
        userStore.setProfilePhoto(profile.value.profile_photo);

        if (photoInput.value) {
            photoInput.value.value = "";
        }

        successMessage.value = "Your profile has been updated successfully.";
    } catch (error) {
        errorMessage.value = "Failed to update profile. Please check your inputs.";
        console.error(error);
    } finally {
        saving.value = false;
    }
};

onMounted(() => {
    loadProfile();
});

onBeforeUnmount(() => {
    if (temporaryPhotoUrl.value) {
        URL.revokeObjectURL(temporaryPhotoUrl.value);
    }
});
</script>

<template>
    <section class="space-y-6">

        <!-- Page Heading -->
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <div class="mb-2 flex items-center gap-2">
                    <span class="rounded-lg bg-blue-100 p-2 text-blue-600">
                        <UserRound :size="20" />
                    </span>
                    <span class="text-xs font-bold uppercase tracking-widest text-blue-600">
                        Personal Profile
                    </span>
                </div>

                <h2 class="text-2xl font-bold tracking-tight text-slate-800">
                    Personal Details
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Manage your personal information and professional identity.
                </p>
            </div>

            <div class="flex items-center gap-2 rounded-xl border border-blue-100 bg-blue-50 px-4 py-2.5">
                <Sparkles :size="17" class="text-blue-600" />
                <span class="text-xs font-semibold text-blue-700">
                    Profile Information
                </span>
            </div>
        </div>

        <!-- Error Message -->
        <Transition name="message">
            <div v-if="errorMessage"
                class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <AlertCircle :size="19" class="mt-0.5 shrink-0" />
                <p class="flex-1">{{ errorMessage }}</p>
                <button type="button" aria-label="Dismiss error" @click="errorMessage = ''">
                    <X :size="17" />
                </button>
            </div>
        </Transition>

        <!-- Success Message -->
        <Transition name="message">
            <div v-if="successMessage"
                class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                <CheckCircle2 :size="19" class="mt-0.5 shrink-0" />
                <p class="flex-1">{{ successMessage }}</p>
                <button type="button" aria-label="Dismiss success" @click="successMessage = ''">
                    <X :size="17" />
                </button>
            </div>
        </Transition>

        <!-- Loading -->
        <div v-if="loading" class="rounded-2xl border border-slate-200 bg-white py-16 text-center shadow-sm">
            <LoaderCircle :size="32" class="mx-auto animate-spin text-blue-600" />
            <p class="mt-3 text-sm font-medium text-slate-500">
                Loading your profile...
            </p>
        </div>

        <form v-else class="space-y-6" @submit.prevent="saveProfile">

            <!-- Profile Photo Card -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="rounded-xl bg-white p-2.5 text-blue-600 shadow-sm">
                            <Camera :size="20" />
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800">Profile Photo</h3>
                            <p class="text-xs text-slate-500">
                                Upload a professional photo for your profile.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center">
                    <div class="relative mx-auto sm:mx-0">
                        <div
                            class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-2xl border-4 border-white bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 shadow-lg ring-1 ring-slate-200">
                            <img v-if="photoPreview" :src="photoPreview" alt="Profile photo preview"
                                class="h-full w-full object-cover" />
                            <UserRound v-else :size="42" />
                        </div>

                        <span
                            class="absolute -bottom-2 -right-2 rounded-xl border-2 border-white bg-blue-600 p-2 text-white shadow-md">
                            <Camera :size="15" />
                        </span>
                    </div>

                    <div class="flex-1 text-center sm:text-left">
                        <h4 class="font-semibold text-slate-800">
                            Choose your profile picture
                        </h4>
                        <p class="mt-1 text-sm text-slate-500">
                            Use a clear photo so employers can recognize you.
                        </p>

                        <div class="mt-4 flex flex-wrap justify-center gap-2 sm:justify-start">
                            <input ref="photoInput" type="file" accept=".jpg,.jpeg,.png,.webp" class="hidden"
                                @change="handlePhotoChange" />

                            <button type="button"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                                @click="photoInput?.click()">
                                <Upload :size="17" />
                                Upload Photo
                            </button>

                            <button v-if="selectedPhoto" type="button"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                                @click="removeSelectedPhoto">
                                <X :size="16" />
                                Cancel Photo
                            </button>
                        </div>

                        <p class="mt-3 text-xs text-slate-400">
                            JPG, PNG or WEBP · Maximum file size 2MB
                        </p>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5">
                    <div class="rounded-xl bg-blue-50 p-2.5 text-blue-600">
                        <UserRound :size="20" />
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">Personal Information</h3>
                        <p class="text-xs text-slate-400">
                            Your contact and basic information.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">

                    <!-- Phone -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Phone Number
                        </label>
                        <div class="relative">
                            <Phone class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                            <input v-model="form.phone" type="text" maxlength="50" placeholder="Enter your phone number"
                                class="profile-input pl-11" />
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Date of Birth
                        </label>
                        <div class="relative">
                            <CalendarDays class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"
                                :size="18" />
                            <input v-model="form.date_of_birth" type="date"
                                :max="new Date().toISOString().split('T')[0]" class="profile-input pl-11" />
                        </div>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Gender
                        </label>
                        <div class="relative">
                            <VenusAndMars class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"
                                :size="18" />
                            <select v-model="form.gender" class="profile-input appearance-none pl-11">
                                <option value="">Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- City -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            City
                        </label>
                        <div class="relative">
                            <MapPin class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                            <input v-model="form.city" type="text" maxlength="100" placeholder="Enter your city"
                                class="profile-input pl-11" />
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Full Address
                        </label>
                        <div class="relative">
                            <MapPin class="absolute left-3.5 top-4 text-slate-400" :size="18" />
                            <textarea v-model="form.address" rows="3" placeholder="Enter your full address"
                                class="profile-input resize-y pl-11" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5">
                    <div class="rounded-xl bg-indigo-50 p-2.5 text-indigo-600">
                        <BriefcaseBusiness :size="20" />
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">Professional Information</h3>
                        <p class="text-xs text-slate-400">
                            Introduce your professional background to employers.
                        </p>
                    </div>
                </div>

                <div class="space-y-5 p-6">

                    <!-- Professional Title -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Professional Title
                        </label>
                        <div class="relative">
                            <BriefcaseBusiness class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"
                                :size="18" />
                            <input v-model="form.professional_title" type="text" maxlength="255"
                                placeholder="e.g. Junior Web Developer" class="profile-input pl-11" />
                        </div>
                    </div>

                    <!-- Bio -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Professional Bio
                        </label>
                        <div class="relative">
                            <FileText class="absolute left-3.5 top-4 text-slate-400" :size="18" />
                            <textarea v-model="form.bio" rows="5"
                                placeholder="Tell employers about your experience, strengths and career goals..."
                                class="profile-input resize-y pl-11" />
                        </div>
                        <p class="mt-1.5 text-xs text-slate-400">
                            Write a short introduction about yourself.
                        </p>
                    </div>

                    <!-- Salary and Experience -->
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Expected Salary
                            </label>
                            <div class="relative">
                                <Wallet class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                                <input v-model.number="form.expected_salary" type="number" min="0" step="0.01"
                                    placeholder="Enter expected salary" class="profile-input pl-11" />
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Experience Years
                            </label>
                            <div class="relative">
                                <Clock3 class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                                <input v-model.number="form.experience_years" type="number" min="0" max="999.9"
                                    step="0.1" placeholder="e.g. 2" class="profile-input pl-11" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-5">
                    <div class="rounded-xl bg-violet-50 p-2.5 text-violet-600">
                        <Globe :size="20" />
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">Social & Portfolio Links</h3>
                        <p class="text-xs text-slate-400">
                            Let employers explore your professional presence.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            LinkedIn URL
                        </label>
                        <div class="relative">
                            <Linkedin class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                            <input v-model="form.linkedin_url" type="url" placeholder="https://linkedin.com/in/yourname"
                                class="profile-input pl-11" />
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Portfolio URL
                        </label>
                        <div class="relative">
                            <Globe class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                            <input v-model="form.portfolio_url" type="url" placeholder="https://yourportfolio.com"
                                class="profile-input pl-11" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Actions -->
            <div
                class="flex flex-col-reverse items-stretch justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:px-6">
                <p class="text-xs text-slate-400">
                    <span class="font-semibold text-slate-600">Tip:</span>
                    Keep your profile information up to date.
                </p>

                <button type="submit" :disabled="saving"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:from-blue-700 hover:to-indigo-700 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0">
                    <LoaderCircle v-if="saving" :size="18" class="animate-spin" />
                    <Save v-else :size="18" />
                    {{ saving ? "Saving Changes..." : "Save Profile Changes" }}
                </button>
            </div>
        </form>
    </section>
</template>

<style scoped>
.profile-input {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    background-color: #fff;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    font-size: 0.875rem;
    color: #334155;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.profile-input::placeholder {
    color: #94a3b8;
}

.profile-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgb(59 130 246 / 12%);
}

.profile-input:disabled {
    cursor: not-allowed;
    background-color: #f8fafc;
}

.message-enter-active,
.message-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.message-enter-from,
.message-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}
</style>
