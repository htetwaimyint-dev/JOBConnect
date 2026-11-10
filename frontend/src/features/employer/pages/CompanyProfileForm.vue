<script setup lang="ts">
import { computed, reactive, ref, watch, onBeforeUnmount } from "vue";
import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";

import type { Company } from "../types/company.types.ts";
import type { CompanyForm, GalleryImage } from "../types/company-form.types.ts";
import { companyService } from "@/services/company.service";

import CompanyBasicInfoForm from "../components/CompanyBasicInfoForm.vue";
import CompanyOverviewForm from "../components/CompanyOverviewForm.vue";
import CompanyBrandingForm from "../components/CompanyBrandingForm.vue";
import CompanyGalleryForm from "../components/CompanyGalleryForm.vue";

import BaseButton from "@/components/common/BaseButton.vue";

// --------------------------------------------------
// Configuration
// --------------------------------------------------

const MAX_FILE_SIZE = 5 * 1024 * 1024;
const MAX_GALLERY_IMAGES = 10;

const ALLOWED_IMAGE_TYPES = ["image/jpeg", "image/png", "image/webp"];

const queryClient = useQueryClient();

// --------------------------------------------------
// Form state
// --------------------------------------------------

function createEmptyForm(): CompanyForm {
    return {
        name: "",
        tagline: "",
        industry: "",
        website: "",
        company_email: "",
        phone: "",
        description: "",
        address: "",
        city: "",
        foundedYear: null,
        employeeCount: "",
    };
}

const form = reactive<CompanyForm>(createEmptyForm());

const logoFile = ref<File | null>(null);
const coverFile = ref<File | null>(null);

const logoPreview = ref<string | null>(null);
const coverPreview = ref<string | null>(null);

const existingGallery = ref<string[]>([]);
const newGallery = ref<GalleryImage[]>([]);

const errorMessage = ref("");
const successMessage = ref("");

const isEditMode = computed(() => !!companyQuery.data.value);

// --------------------------------------------------
// Helpers
// --------------------------------------------------

function revokePreview(url: string | null) {
    if (url?.startsWith("blob:")) {
        URL.revokeObjectURL(url);
    }
}

function clearMessages() {
    errorMessage.value = "";
    successMessage.value = "";
}

function setLogoPreview(url: string | null) {
    revokePreview(logoPreview.value);
    logoPreview.value = url;
}

function setCoverPreview(url: string | null) {
    revokePreview(coverPreview.value);
    coverPreview.value = url;
}

function validateImage(file: File): string | null {
    if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
        return "Only JPG, PNG, and WEBP images are allowed.";
    }

    if (file.size > MAX_FILE_SIZE) {
        return "Image size must not exceed 5MB.";
    }

    return null;
}

function populateForm(company: Company) {
    form.name = company.name ?? "";
    form.tagline = company.tagline ?? "";
    form.industry = company.industry ?? "";
    form.website = company.website ?? "";
    form.company_email = company.company_email ?? "";
    form.phone = company.phone ?? "";
    form.description = company.description ?? "";
    form.address = company.address ?? "";
    form.city = company.city ?? "";
    form.foundedYear = company.foundedYear ?? null;
    form.employeeCount = company.employeeCount ?? "";

    logoFile.value = null;
    coverFile.value = null;

    setLogoPreview(company.logo ?? null);
    setCoverPreview(company.coverImage ?? null);

    existingGallery.value = [...(company.gallery ?? [])];

    newGallery.value.forEach((image: { preview: string | null }) => {
        revokePreview(image.preview);
    });

    newGallery.value = [];
}

function resetEmptyForm() {
    Object.assign(form, createEmptyForm());

    logoFile.value = null;
    coverFile.value = null;

    setLogoPreview(null);
    setCoverPreview(null);

    existingGallery.value = [];

    newGallery.value.forEach((image: { preview: string | null }) => {
        revokePreview(image.preview);
    });

    newGallery.value = [];
}

// --------------------------------------------------
// Fetch company profile
// --------------------------------------------------

const companyQuery = useQuery({
    queryKey: ["company", "profile"],

    queryFn: async (): Promise<Company | null> => {
        try {
            return await companyService.getCompanyProfile();
        } catch (error) {
            const status = (
                error as {
                    response?: {
                        status?: number;
                    };
                }
            )?.response?.status;

            // No company profile exists yet.
            if (status === 404) {
                return null;
            }

            throw error;
        }
    },

    staleTime: 1000 * 60 * 5,
    retry: 1,
});

// Populate the form when the initial profile loads.
let initialized = false;

watch(
    () => companyQuery.data.value,
    (company) => {
        if (initialized) return;

        if (company) {
            populateForm(company);
        }

        initialized = true;
    },
    { immediate: true },
);

// --------------------------------------------------
// Image selection
// --------------------------------------------------

function handleLogoSelect(file: File) {
    clearMessages();

    const validationError = validateImage(file);

    if (validationError) {
        errorMessage.value = validationError;
        return;
    }

    logoFile.value = file;
    setLogoPreview(URL.createObjectURL(file));
}

function handleCoverSelect(file: File) {
    clearMessages();

    const validationError = validateImage(file);

    if (validationError) {
        errorMessage.value = validationError;
        return;
    }

    coverFile.value = file;
    setCoverPreview(URL.createObjectURL(file));
}

// --------------------------------------------------
// Gallery management
// --------------------------------------------------

function handleGalleryAdd(files: FileList) {
    clearMessages();

    const selectedFiles = Array.from(files);

    const totalImages = existingGallery.value.length + newGallery.value.length;

    for (const file of selectedFiles) {
        if (totalImages + newGallery.value.length >= MAX_GALLERY_IMAGES) {
            errorMessage.value = `You can upload a maximum of ${MAX_GALLERY_IMAGES} gallery images.`;
            break;
        }

        const validationError = validateImage(file);

        if (validationError) {
            errorMessage.value = `${file.name}: ${validationError}`;
            continue;
        }

        newGallery.value.push({
            file,
            preview: URL.createObjectURL(file),
        });
    }
}

function removeExistingGallery(index: number) {
    existingGallery.value.splice(index, 1);
}

function removeNewGallery(index: number) {
    const image = newGallery.value[index];

    if (image) {
        revokePreview(image.preview);
        newGallery.value.splice(index, 1);
    }
}

// --------------------------------------------------
// FormData
// --------------------------------------------------

function buildFormData(): FormData {
    const data = new FormData();

    data.append("name", form.name.trim());
    data.append("tagline", form.tagline.trim());
    data.append("industry", form.industry.trim());
    data.append("website", form.website.trim());
    data.append("company_email", form.company_email.trim());
    data.append("phone", form.phone.trim());
    data.append("description", form.description.trim());
    data.append("address", form.address.trim());
    data.append("city", form.city.trim());

    if (form.foundedYear !== null) {
        data.append("foundedYear", String(form.foundedYear));
    }

    data.append("employeeCount", form.employeeCount);

    // Upload logo only when a new file was selected.
    if (logoFile.value) {
        data.append("logo", logoFile.value);
    }

    // Upload cover only when a new file was selected.
    if (coverFile.value) {
        data.append("cover_image", coverFile.value);
    }

    // Existing gallery URLs retained by the user.
    existingGallery.value.forEach((image) => {
        data.append("existing_gallery[]", image);
    });

    // Newly uploaded gallery files.
    newGallery.value.forEach((image: { file: string | Blob }) => {
        data.append("gallery[]", image.file);
    });

    return data;
}

// --------------------------------------------------
// Validation
// --------------------------------------------------

function validateForm(): boolean {
    if (!form.name.trim()) {
        errorMessage.value = "Company name is required.";
        return false;
    }

    if (
        form.foundedYear !== null &&
        (Number(form.foundedYear) < 1800 ||
            Number(form.foundedYear) > new Date().getFullYear())
    ) {
        errorMessage.value = "Please enter a valid founded year.";
        return false;
    }

    if (
        existingGallery.value.length + newGallery.value.length >
        MAX_GALLERY_IMAGES
    ) {
        errorMessage.value = `You can upload a maximum of ${MAX_GALLERY_IMAGES} gallery images.`;
        return false;
    }

    return true;
}

// --------------------------------------------------
// Save mutation
// --------------------------------------------------

interface SaveVariables {
    data: FormData;
    mode: "create" | "update";
}

const mutation = useMutation({
    mutationFn: async ({ data, mode }: SaveVariables) => {
        if (mode === "update") {
            return companyService.updateCompanyProfile(data);
        }

        return companyService.createCompanyProfile(data);
    },

    onSuccess: (company, variables) => {
        queryClient.setQueryData(["company", "profile"], company);

        populateForm(company);

        successMessage.value =
            variables.mode === "update"
                ? "Company profile updated successfully."
                : "Company profile created successfully.";

        errorMessage.value = "";
    },

    onError: (error: unknown) => {
        const apiError = error as {
            response?: {
                data?: {
                    message?: string;
                };
            };
            message?: string;
        };

        errorMessage.value =
            apiError.response?.data?.message ||
            apiError.message ||
            "Something went wrong. Please try again.";
    },
});

const isSaving = computed(() => mutation.isPending.value);

// --------------------------------------------------
// Submit
// --------------------------------------------------

function submitForm() {
    clearMessages();

    if (!validateForm()) {
        return;
    }

    const mode = isEditMode.value ? "update" : "create";

    mutation.mutate({
        data: buildFormData(),
        mode,
    });
}

// --------------------------------------------------
// Reset
// --------------------------------------------------

function resetForm() {
    clearMessages();

    const company = companyQuery.data.value;

    if (company) {
        populateForm(company);
    } else {
        resetEmptyForm();
    }
}

// --------------------------------------------------
// Cleanup
// --------------------------------------------------

onBeforeUnmount(() => {
    revokePreview(logoPreview.value);
    revokePreview(coverPreview.value);

    newGallery.value.forEach((image: { preview: string | null }) => {
        revokePreview(image.preview);
    });
});
</script>

<template>
    <div class="mx-auto w-full max-w-5xl space-y-3 pb-8">
        <!-- Page heading -->
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">
                    Company profile
                </h1>

                <p class="mt-0.5 text-xs text-gray-500">
                    Manage your company's public profile and branding.
                </p>
            </div>

            <span
                v-if="isEditMode"
                class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700"
            >
                Editing profile
            </span>

            <span
                v-else-if="!companyQuery.isLoading.value"
                class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700"
            >
                New profile
            </span>
        </div>

        <!-- Initial loading -->
        <div
            v-if="companyQuery.isLoading.value"
            class="rounded-lg border border-gray-200 bg-white px-4 py-10 text-center"
        >
            <div
                class="mx-auto h-6 w-6 animate-spin rounded-full border-2 border-blue-600 border-r-transparent"
            />

            <p class="mt-3 text-sm text-gray-500">Loading company profile...</p>
        </div>

        <!-- Fetch error -->
        <div
            v-else-if="companyQuery.isError.value"
            class="rounded-lg border border-red-200 bg-red-50 p-4"
        >
            <p class="text-sm font-medium text-red-700">
                Unable to load company profile.
            </p>

            <p class="mt-1 text-xs text-red-600">
                {{ companyQuery.error.value?.message || "Please try again." }}
            </p>

            <div class="mt-3">
                <BaseButton
                    size="28"
                    color="danger"
                    variant="outline"
                    @click="companyQuery.refetch()"
                >
                    Retry
                </BaseButton>
            </div>
        </div>

        <!-- Form -->
        <form v-else class="space-y-3" @submit.prevent="submitForm">
            <!-- Success message -->
            <div
                v-if="successMessage"
                class="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-3 py-2.5"
                role="status"
            >
                <span class="text-sm text-green-700">
                    {{ successMessage }}
                </span>
            </div>

            <!-- Error message -->
            <div
                v-if="errorMessage"
                class="rounded-lg border border-red-200 bg-red-50 px-3 py-2.5"
                role="alert"
            >
                <p class="text-sm text-red-700">
                    {{ errorMessage }}
                </p>
            </div>

            <!-- Basic information -->
            <CompanyBasicInfoForm
                v-model:form="form"
                :logo-preview="logoPreview"
                @select-logo="handleLogoSelect"
            />

            <!-- Company overview -->
            <CompanyOverviewForm v-model:form="form" />

            <!-- Branding -->
            <CompanyBrandingForm
                :cover-preview="coverPreview"
                @select-cover="handleCoverSelect"
            />

            <!-- Gallery -->
            <CompanyGalleryForm
                :existing-images="existingGallery"
                :new-images="newGallery"
                @remove-existing="removeExistingGallery"
                @remove-new="removeNewGallery"
            />
            <!-- @add-images="handleGalleryAdd" -->

            <!-- Actions -->
            <div
                class="sticky bottom-0 z-10 flex items-center justify-end gap-2 border-t border-gray-200 bg-white/95 py-3 backdrop-blur"
            >
                <BaseButton
                    type="button"
                    size="32"
                    color="neutral"
                    variant="outline"
                    :disabled="isSaving"
                    @click="resetForm"
                >
                    Cancel
                </BaseButton>

                <BaseButton
                    type="submit"
                    size="32"
                    color="primary"
                    :loading="isSaving"
                    :disabled="isSaving"
                >
                    {{ isEditMode ? "Save changes" : "Create profile" }}
                </BaseButton>
            </div>
        </form>
    </div>
</template>
