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
        address: "",
        city: "",
        foundedYear: 0,
        employeeCount: "",
        sections: [],
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

    const errors = ref<Record<string, string[]>>({});

    const hasChanges = computed(
        () =>
            !!logoFile.value ||
            !!coverFile.value ||
            newGallery.value.length > 0,
    );

    function getFieldError(field: string): string {
        return errors.value[field]?.[0] ?? "";
    }

    function clearErrors() {
        errors.value = {};
    }

    function setErrors(newErrors: Record<string, string[]>) {
        errors.value = newErrors;
    }

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
        if (!company) {
            reset();
            return;
        }

        let sections = [];

        if (company.description) {
            try {
                const parsed = JSON.parse(company.description);

                if (Array.isArray(parsed)) {
                    sections = parsed;
                }
            } catch {
                // Keep sections empty if description isn't valid JSON.
                sections = [];
            }
        }

        form.value = {
            name: company.name ?? "",
            tagline: company.tagline ?? "",
            industry: company.industry ?? "",
            website: company.website ?? "",
            company_email: company.company_email ?? "",
            phone: company.phone ?? "",
            address: company.address ?? "",
            city: company.city ?? "",

            foundedYear: company.founded_year ?? null,
            employeeCount: company.employee_count ?? "",

            sections,
        };

        logoPreview.value = company.logo;
        coverPreview.value = company.cover_image;

        existingGallery.value = (company.gallery ?? []).filter(
            (image): image is string =>
                typeof image === "string" && image.trim() !== "",
        );
        newGallery.value = [];

        logoFile.value = null;
        coverFile.value = null;

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

    function buildFormData(): FormData {
        const formData = new FormData();

        formData.append("name", form.value.name.trim());
        formData.append("tagline", form.value.tagline.trim());
        formData.append("industry", form.value.industry.trim());
        formData.append("website", form.value.website.trim());
        formData.append("company_email", form.value.company_email.trim());
        formData.append("phone", form.value.phone.trim());
        formData.append("address", form.value.address.trim());
        formData.append("city", form.value.city.trim());

        if (form.value.foundedYear) {
            formData.append("founded_year", form.value.foundedYear.toString());
        }

        formData.append("employee_count", form.value.employeeCount);

        formData.append("description", JSON.stringify(form.value.sections));

        // Company logo
        if (logoFile.value instanceof File) {
            formData.append("logo", logoFile.value);
        }

        // Company cover
        if (coverFile.value instanceof File) {
            formData.append("cover_image", coverFile.value);
        }

        // Newly uploaded gallery images
        newGallery.value.forEach((image) => {
            if (image.file instanceof File) {
                formData.append("gallery[]", image.file);
            }
        });

        // Existing gallery images
        existingGallery.value.forEach((image) => {
            if (typeof image === "string" && image.trim() !== "") {
                formData.append("existing_gallery[]", image.trim());
            }
        });

        return formData;
    }

    function reset() {
        cleanupPreviews();

        form.value = emptyForm();

        logoFile.value = null;
        coverFile.value = null;

        existingGallery.value = [];
        newGallery.value = [];

        errorMessage.value = "";
        successMessage.value = "";

        clearErrors();

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
        errors,
        populate,
        selectImage,
        addGallery,
        removeGallery,
        buildFormData,
        reset,
        cleanupPreviews,
        getFieldError,
        clearErrors,
        setErrors,
    };
});
