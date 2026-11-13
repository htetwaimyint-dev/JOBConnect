<script setup lang="ts">
import { ref, watch, onBeforeUnmount } from "vue";
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
    ChevronDown,
    AlertCircle,
    Upload,
    X,
} from "lucide-vue-next";

import AppModal from "@/components/common/AppModal.vue";

import type {
    JobSeekerProfile,
    UpdateProfileData,
} from "../types/profile.types";

const props = defineProps<{
    show: boolean;
    profile: JobSeekerProfile | null;
    isSaving?: boolean;
}>();

const emit = defineEmits<{
    close: [];
    save: [data: UpdateProfileData];
}>();

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
    profile_photo: null,
});

const selectedPhoto = ref<File | null>(null);
const photoPreview = ref<string | null>(null);
const photoInput = ref<HTMLInputElement | null>(null);
const temporaryPhotoUrl = ref<string | null>(null);
const errorMessage = ref("");

const resetForm = () => {
    const profile = props.profile;

    form.value = {
        phone: profile?.phone ?? "",
        date_of_birth: profile?.date_of_birth ?? "",
        gender: profile?.gender ?? "",
        address: profile?.address ?? "",
        city: profile?.city ?? "",
        professional_title: profile?.professional_title ?? "",
        bio: profile?.bio ?? "",
        expected_salary: profile?.expected_salary ?? null,
        experience_years: profile?.experience_years ?? null,
        linkedin_url: profile?.linkedin_url ?? "",
        portfolio_url: profile?.portfolio_url ?? "",
        profile_photo: null,
    };

    selectedPhoto.value = null;
    photoPreview.value = profile?.profile_photo ?? null;
    errorMessage.value = "";

    if (photoInput.value) {
        photoInput.value.value = "";
    }

    if (temporaryPhotoUrl.value) {
        URL.revokeObjectURL(temporaryPhotoUrl.value);
        temporaryPhotoUrl.value = null;
    }
};

watch(
    () => [props.show, props.profile] as const,
    ([show]) => {
        if (show) {
            resetForm();
        }
    },
    { immediate: true },
);

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

    photoPreview.value = props.profile?.profile_photo ?? null;

    if (photoInput.value) {
        photoInput.value.value = "";
    }
};

const handleSubmit = () => {
    errorMessage.value = "";

    emit("save", {
        ...form.value,
        profile_photo: selectedPhoto.value,
    });
};

onBeforeUnmount(() => {
    if (temporaryPhotoUrl.value) {
        URL.revokeObjectURL(temporaryPhotoUrl.value);
    }
});
</script>

<template>
    <AppModal :show="show" title="Edit Personal Information"
        description="Update your personal details and professional profile." max-width="max-w-3xl"
        @close="emit('close')">
        <form class="space-y-6" @submit.prevent="handleSubmit">

            <!-- Error Message -->
            <div v-if="errorMessage"
                class="flex items-start gap-2 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                <AlertCircle :size="18" class="mt-0.5 shrink-0" />
                <p>{{ errorMessage }}</p>
            </div>

            <!-- Profile Photo -->
            <div class="rounded-2xl border border-slate-200 p-5">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <div
                        class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-blue-50 text-blue-600">
                        <img v-if="photoPreview" :src="photoPreview" alt="Profile photo preview"
                            class="h-full w-full object-cover" />
                        <UserRound v-else :size="38" />
                    </div>

                    <div class="flex-1">
                        <h3 class="font-semibold text-slate-800">
                            Profile Photo
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Upload a professional photo for your profile.
                        </p>

                        <input ref="photoInput" type="file" accept=".jpg,.jpeg,.png,.webp" class="hidden"
                            @change="handlePhotoChange" />

                        <div class="mt-3 flex flex-wrap gap-2">
                            <button type="button"
                                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
                                @click="photoInput?.click()">
                                <Upload :size="16" />
                                Upload Photo
                            </button>

                            <button v-if="selectedPhoto" type="button"
                                class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
                                @click="removeSelectedPhoto">
                                <X :size="16" />
                                Cancel Photo
                            </button>
                        </div>

                        <p class="mt-2 text-xs text-slate-400">
                            JPG, PNG or WEBP · Maximum 2MB
                        </p>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <UserRound :size="19" class="text-blue-600" />
                    <h3 class="font-bold text-slate-800">
                        Personal Information
                    </h3>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="form-label">Phone Number</label>
                        <div class="input-wrapper">
                            <Phone :size="17" class="input-icon" />
                            <input v-model="form.phone" type="text" maxlength="50" placeholder="Enter your phone number"
                                class="form-input" />
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Date of Birth</label>
                        <div class="input-wrapper">
                            <CalendarDays :size="17" class="input-icon" />
                            <input v-model="form.date_of_birth" type="date"
                                :max="new Date().toISOString().split('T')[0]" class="form-input" />
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Gender</label>

                        <div class="input-wrapper">
                            <VenusAndMars :size="17" class="input-icon" />

                            <select v-model="form.gender" class="form-input appearance-none cursor-pointer pr-10">
                                <option value="">Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>

                            <ChevronDown :size="17" class="pointer-events-none absolute right-3 text-slate-400" />
                        </div>
                    </div>

                    <div>
                        <label class="form-label">City</label>
                        <div class="input-wrapper">
                            <MapPin :size="17" class="input-icon" />
                            <input v-model="form.city" type="text" maxlength="100" placeholder="Enter your city"
                                class="form-input" />
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label">Full Address</label>
                        <div class="input-wrapper items-start">
                            <MapPin :size="17" class="input-icon top-4" />
                            <textarea v-model="form.address" rows="3" placeholder="Enter your full address"
                                class="form-input resize-y" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <BriefcaseBusiness :size="19" class="text-indigo-600" />
                    <h3 class="font-bold text-slate-800">
                        Professional Information
                    </h3>
                </div>

                <div>
                    <label class="form-label">Professional Title</label>
                    <div class="input-wrapper">
                        <BriefcaseBusiness :size="17" class="input-icon" />
                        <input v-model="form.professional_title" type="text" maxlength="255"
                            placeholder="e.g. Junior Web Developer" class="form-input" />
                    </div>
                </div>

                <div>
                    <label class="form-label">Professional Bio</label>
                    <div class="input-wrapper items-start">
                        <FileText :size="17" class="input-icon top-4" />
                        <textarea v-model="form.bio" rows="4"
                            placeholder="Tell employers about your experience, strengths and career goals..."
                            class="form-input resize-y" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="form-label">Expected Salary</label>
                        <div class="input-wrapper">
                            <Wallet :size="17" class="input-icon" />
                            <input v-model.number="form.expected_salary" type="number" min="0" step="0.01"
                                placeholder="Enter expected salary" class="form-input" />
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Experience Years</label>
                        <div class="input-wrapper">
                            <Clock3 :size="17" class="input-icon" />
                            <input v-model.number="form.experience_years" type="number" min="0" max="999.9" step="0.1"
                                placeholder="e.g. 2" class="form-input" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <Globe :size="19" class="text-violet-600" />
                    <h3 class="font-bold text-slate-800">
                        Social & Portfolio Links
                    </h3>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="form-label">LinkedIn URL</label>
                        <div class="input-wrapper">
                            <Linkedin :size="17" class="input-icon" />
                            <input v-model="form.linkedin_url" type="url" placeholder="https://linkedin.com/in/yourname"
                                class="form-input" />
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Portfolio URL</label>
                        <div class="input-wrapper">
                            <Globe :size="17" class="input-icon" />
                            <input v-model="form.portfolio_url" type="url" placeholder="https://yourportfolio.com"
                                class="form-input" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-100 pt-5 sm:flex-row">
                <button type="button" :disabled="isSaving"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 disabled:opacity-50"
                    @click="emit('close')">
                    <X :size="17" />
                    Cancel
                </button>

                <button type="submit" :disabled="isSaving"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                    <LoaderCircle v-if="isSaving" :size="17" class="animate-spin" />
                    <Save v-else :size="17" />
                    {{ isSaving ? "Saving Changes..." : "Save Changes" }}
                </button>
            </div>
        </form>
    </AppModal>
</template>

<style scoped>
.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #334155;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 0.875rem;
    z-index: 1;
    color: #94a3b8;
    pointer-events: none;
}

.form-input {
    width: 100%;
    min-width: 0;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    background-color: white;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    font-size: 0.875rem;
    color: #334155;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-input::placeholder {
    color: #94a3b8;
}

.form-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgb(59 130 246 / 12%);
}
</style>
