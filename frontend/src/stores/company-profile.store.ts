import { defineStore } from "pinia";
import { ref, computed, onScopeDispose } from "vue";
import type { Company } from "@/features/employer/types/company.types";
import type {
    CompanyForm,
    GalleryImage,
} from "@/features/employer/types/company-form.types";

function emptyForm(): CompanyForm {
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

export const useCompanyProfileStore = defineStore("company-profile", () => {
    const form = ref<CompanyForm>(emptyForm());

    const logoFile = ref<File | null>(null);
    const coverFile = ref<File | null>(null);

    const logoPreview = ref<string | null>(null);
    const coverPreview = ref<string | null>(null);

    const existingGallery = ref<string[]>([]);
    const newGallery = ref<GalleryImage[]>([]);

    const errorMessage = ref("");
    const successMessage = ref("");

    const isEditMode = ref(false);

    const hasChanges = computed(
        () =>
            !!logoFile.value ||
            !!coverFile.value ||
            newGallery.value.length > 0,
    );

    function revokePreview(url: string | null) {
        if (url?.startsWith("blob:")) {
            URL.revokeObjectURL(url);
        }
    }

    function setPreview(file: File, type: "logo" | "cover") {
        const preview = URL.createObjectURL(file);

        if (type === "logo") {
            revokePreview(logoPreview.value);
            logoFile.value = file;
            logoPreview.value = preview;
        } else {
            revokePreview(coverPreview.value);
            coverFile.value = file;
            coverPreview.value = preview;
        }
    }

    function populate(company: Company | null) {
        cleanupPreviews();

        if (!company) {
            form.value = emptyForm();
            isEditMode.value = false;
            existingGallery.value = [];
            return;
        }

        form.value = {
            name: company.name ?? "",
            tagline: company.tagline ?? "",
            industry: company.industry ?? "",
            website: company.website ?? "",
            company_email: company.company_email ?? "",
            phone: company.phone ?? "",
            description: company.description ?? "",
            address: company.address ?? "",
            city: company.city ?? "",
            foundedYear: company.foundedYear ?? null,
            employeeCount: company.employeeCount ?? "",
        };

        logoPreview.value = company.logo;
        coverPreview.value = company.coverImage ?? null;
        existingGallery.value = [...(company.gallery ?? [])];

        isEditMode.value = true;
    }

    function cleanupPreviews() {
        revokePreview(logoPreview.value);
        revokePreview(coverPreview.value);

        newGallery.value.forEach((image) => revokePreview(image.preview));

        logoPreview.value = null;
        coverPreview.value = null;
        newGallery.value = [];
    }

    function selectImage(file: File, type: "logo" | "cover") {
        const validTypes = ["image/jpeg", "image/png", "image/webp"];

        if (!validTypes.includes(file.type)) {
            errorMessage.value = "Please select a JPG, PNG, or WEBP image.";
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            errorMessage.value = "Image must be smaller than 5MB.";
            return;
        }

        errorMessage.value = "";
        setPreview(file, type);
    }

    function addGallery(files: File[]) {
        errorMessage.value = "";

        for (const file of files) {
            if (existingGallery.value.length + newGallery.value.length >= 10) {
                errorMessage.value = "You can upload a maximum of 10 images.";
                break;
            }

            if (
                !["image/jpeg", "image/png", "image/webp"].includes(
                    file.type,
                ) ||
                file.size > 5 * 1024 * 1024
            ) {
                errorMessage.value =
                    "Gallery images must be JPG, PNG, or WEBP and under 5MB.";
                continue;
            }

            newGallery.value.push({
                file,
                preview: URL.createObjectURL(file),
            });
        }
    }

    function removeGallery(index: number, isNew = false) {
        if (isNew) {
            const image = newGallery.value[index];

            if (image) revokePreview(image.preview);

            newGallery.value.splice(index, 1);
        } else {
            existingGallery.value.splice(index, 1);
        }
    }

    function buildFormData() {
        const data = new FormData();

        Object.entries(form.value).forEach(([key, value]) => {
            data.append(key, value == null ? "" : String(value).trim());
        });

        if (logoFile.value) {
            data.append("logo", logoFile.value);
        }

        if (coverFile.value) {
            data.append("cover_image", coverFile.value);
        }

        newGallery.value.forEach(({ file }) => {
            data.append("gallery[]", file);
        });

        existingGallery.value.forEach((image) => {
            data.append("existing_gallery[]", image);
        });

        return data;
    }

    function reset() {
        cleanupPreviews();

        form.value = emptyForm();
        logoFile.value = null;
        coverFile.value = null;
        existingGallery.value = [];
        errorMessage.value = "";
        successMessage.value = "";
        isEditMode.value = false;
    }

    onScopeDispose(cleanupPreviews);

    return {
        form,
        logoFile,
        coverFile,
        logoPreview,
        coverPreview,
        existingGallery,
        newGallery,
        errorMessage,
        successMessage,
        isEditMode,
        hasChanges,
        populate,
        selectImage,
        addGallery,
        removeGallery,
        buildFormData,
        reset,
        cleanupPreviews,
    };
});
