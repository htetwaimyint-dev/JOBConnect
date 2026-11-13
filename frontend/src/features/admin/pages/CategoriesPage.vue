<script setup lang="ts">
import { ref, reactive, onMounted } from "vue";
import categoryRepository from "../repositories/categoryRepository";
import { Upload, Trash2, Folder } from "lucide-vue-next";

const categories = ref<any[]>([]);
const isLoading = ref(false);

const searchQuery = ref("");
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

//these state variables at the top of <script setup>
const isViewModalOpen = ref(false);
const selectedCategory = ref<any | null>(null);
const isLogoRemoved = ref(false);

// Modal & Form State Variables ---
const isFormModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditId = ref<number | null>(null);

const formData = reactive({
  name: "",
  logo: null as File | string | null,
});

// File input reference
const logoInput = ref<HTMLInputElement | null>(null);

// Preview URL
const previewLogo = ref<string | null>(null);

// Open file picker
const openLogoPicker = () => {
  logoInput.value?.click();
};

// Handle selected logo
const handleLogoChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  if (!file) return;

  // Check file size (2 MB)
  if (file.size > 2 * 1024 * 1024) {
    alert("Image size must not exceed 2 MB.");
    target.value = "";
    return;
  }

  // Save selected file
  formData.logo = file;
  isLogoRemoved.value = false;

  // Show preview
  if (previewLogo.value) {
    URL.revokeObjectURL(previewLogo.value);
  }

  previewLogo.value = URL.createObjectURL(file);
};

// Display selected or existing logo
const displayedLogo = () => {
  if (previewLogo.value) {
    return previewLogo.value;
  }

  if (typeof formData.logo === "string" && formData.logo) {
    return getImageUrl(formData.logo);
  }

  return null;
};

// Remove logo
const removeLogo = () => {
  formData.logo = null;
  previewLogo.value = null;
  isLogoRemoved.value = true;

  if (logoInput.value) {
    logoInput.value.value = "";
  }
};

const getImageUrl = (path: string) => {
  if (path.startsWith("http://") || path.startsWith("https://")) {
    return path;
  }
  return `http://localhost:8000/storage/${path}`;
};

const perPage = ref(10);
const getCategories = async (page = 1) => {
  isLoading.value = true;
  try {
    const result = await categoryRepository.getCategories({
      page: page,
      search: searchQuery.value.trim() || undefined,
      per_page: perPage.value,
    });

    // const result = response.data;

    categories.value = result.data;
    pagination.current_page = result.current_page;
    pagination.last_page = result.last_page;
    pagination.total = result.total;
    pagination.from = result.from || 0;
    pagination.to = result.to || 0;
  } catch (error) {
    console.error("API Error:", error);
  } finally {
    isLoading.value = false;
  }
};

const handlePerPageChange = () => {
  getCategories(1);
};

// Debounce Search
const handleSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout);

  searchTimeout = setTimeout(() => {
    getCategories(1);
  }, 400);
};

const changePage = (page: number) => {
  if (
    page >= 1 &&
    page <= pagination.last_page &&
    page !== pagination.current_page
  ) {
    getCategories(page);
  }
};

//this function to open the modal and fetch details
// const handleView = async (id: number) => {
//   isViewModalOpen.value = true;
//   isLoadingDetails.value = true;
//   selectedCategory.value = null;

//   try {
//     const response = await categoryRepository.getCategoryById(id);
//     // Your Laravel show() returns { data: { ... } }
//     selectedCategory.value = response.data;
//   } catch (error) {
//     console.error("Failed to fetch category details:", error);
//     alert("Could not load category details.");
//     isViewModalOpen.value = false;
//   } finally {
//     isLoadingDetails.value = false;
//   }
// };

const closeViewModal = () => {
  isViewModalOpen.value = false;
  selectedCategory.value = null;
};

// --- NEW: Open Modal for Add or Edit ---
const openFormModal = (mode: "add" | "edit", category: any = null) => {
  isFormModalOpen.value = true;
  previewLogo.value = null;
  isLogoRemoved.value = false;

  previewLogo.value = null;
  if (logoInput.value) {
    logoInput.value.value = "";
  }

  if (mode === "edit" && category) {
    isEditMode.value = true;
    currentEditId.value = category.id;
    formData.name = category.name;
    formData.logo = category.logo || null;
  } else {
    isEditMode.value = false;
    currentEditId.value = null;
    formData.name = "";
    formData.logo = null;
  }
};

const closeFormModal = () => {
  isFormModalOpen.value = false;
  previewLogo.value = null;
  formData.name = "";
  formData.logo = null;
  if (logoInput.value) {
    logoInput.value.value = "";
  }
};

//  Save Category logic
const saveCategory = async () => {
  if (!formData.name.trim()) {
    alert("Category Name is required.");
    return;
  }

  try {
    const data = new FormData();

    data.append("name", formData.name);

    if (formData.logo instanceof File) {
      data.append("logo", formData.logo);
    } else if (isLogoRemoved.value) {
      // Tell Laravel to delete the existing logo
      data.append("remove_logo", "1");
    }

    if (isEditMode.value && currentEditId.value) {
      data.append("_method", "PUT");

      await categoryRepository.updateCategory(currentEditId.value, data);
    } else {
      await categoryRepository.createCategory(data);
    }

    await getCategories();
    closeFormModal();
  } catch (error: any) {
    console.error("Save Error:", error.response || error);

    const message = error.response?.data?.message || "Failed to save category.";

    alert(message);
  }
};

const handleDelete = async (id: number) => {
  if (!confirm("Are you sure you want to delete this category?")) {
    return;
  }

  try {
    await categoryRepository.deleteCategory(id);
    categories.value = categories.value.filter(
      (category) => category.id !== id,
    );
  } catch (error: any) {
    console.error("Full Delete Error:", error.response || error);
    // Show the real message from Laravel (422, 404, 401, or 500)
    const message =
      error.response?.data?.message || "Failed to delete category.";
    alert(message);
  }
};

onMounted(() => {
  getCategories();
});
</script>

<template>
  <!-- <div class="min-h-screen bg-gray-50 p-8 font-sans"> -->
  <!-- Main Card -->
  <div
    class="max-w-5xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200"
  >
    <!-- Header -->
    <div class="p-6 border-b border-gray-200 flex justify-between items-start">
      <div>
        <h2 class="text-2xl font-semibold text-gray-800">
          Category Management
        </h2>
        <p class="text-gray-500 text-sm mt-1">Manage your job categories</p>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="p-6 pb-2">
      <div class="flex justify-between items-center">
        <!-- Search Bar -->
        <div class="relative w-full max-w-md">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3">
            <svg
              class="w-5 h-5 text-gray-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              ></path>
            </svg>
          </span>
          <input
            v-model="searchQuery"
            @input="handleSearch"
            type="text"
            placeholder="Search categories..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
          />
        </div>

        <!-- Add Button -->
        <button
          @click="openFormModal('add')"
          class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors"
        >
          <svg
            class="w-4 h-4 mr-2"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4v16m8-8H4"
            ></path>
          </svg>
          Add Category
        </button>
      </div>
    </div>

    <!-- Selection Status & Delete Action -->
    <div class="px-6 py-2 flex justify-between items-center">
      <span class="text-sm text-gray-500">3 categories selected</span>
      <button
        class="text-red-500 bg-red-50 hover:bg-red-100 font-medium py-1.5 px-3 rounded-md text-sm transition-colors"
      >
        Delete Selected
      </button>
    </div>

    <!-- Table -->
    <div class="px-6 pb-6 mt-2">
      <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-gray-50 border-b border-gray-200 text-gray-700 text-sm"
            >
              <th></th>
              <th class="py-3 px-4 font-medium">No.</th>
              <th class="py-3 px-4 font-medium">Logo</th>
              <th class="py-3 px-4 font-medium">Name</th>
              <th class="py-3 px-4 font-medium">Option</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(category, index) in categories"
              :key="category.id"
              class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
            >
              <td class="py-4 px-4">
                <input
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
              </td>
              <td class="py-4 px-4 text-gray-600">{{ index + 1 }}</td>
              <th class="py-3 px-4 font-medium text-center">
                <img
                  v-if="category.logo"
                  :src="getImageUrl(category.logo)"
                  :alt="category.name"
                  class="w-10 h-10 object-cover rounded-lg border border-gray-200"
                />
                <!-- <span v-else class="text-xl">📁</span> -->
              </th>
              <td class="py-4 px-4 font-medium text-gray-800">
                {{ category.name }}
              </td>
              <td class="py-4 px-4">
                <div class="flex space-x-3 text-lg">
                  <!-- <button
                    @click="handleView(category.id)"
                    class="text-blue-500 hover:text-blue-700"
                  >
                    👁️
                  </button> -->
                  <button
                    @click="openFormModal('edit', category)"
                    class="text-orange-400 hover:text-orange-600"
                  >
                    ✏️
                  </button>
                  <button
                    @click="handleDelete(category.id)"
                    class="text-red-500 hover:text-red-700"
                  >
                    🗑️
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination Footer -->
    <div
      class="px-6 py-4 border-t border-gray-200 flex justify-between items-center bg-gray-50 rounded-b-xl"
    >
      <span class="text-sm text-gray-500"
        >Showing {{ pagination.from }}–{{ pagination.to }} of
        {{ pagination.total }}</span
      >

      <div class="flex items-center space-x-2">
        <button
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-50"
        >
          Previous
        </button>
        <button
          v-for="page in pagination.last_page"
          :key="page"
          @click="changePage(page)"
          :class="
            page === pagination.current_page
              ? 'px-3 py-1 text-sm border border-blue-500 rounded-md bg-blue-50 text-blue-600 font-medium'
              : 'px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-600 hover:bg-gray-50'
          "
        >
          {{ page }}
        </button>

        <button
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-600 hover:bg-gray-50"
        >
          Next
        </button>

        <!-- <div class="flex items-center space-x-2">
          <span class="text-sm text-gray-500">Show</span>
          <select
            v-model="perPage"
            @change="handlePerPageChange"
            class="px-2 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-700 outline-none focus:border-blue-500"
          >
            <option :value="5">5</option>
            <option :value="10">10</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
          </select>
          <span class="text-sm text-gray-500">entries</span>
        </div> -->
      </div>
    </div>

    <!-- ADD / EDIT MODAL BOX -->

    <div
      v-if="isFormModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <!-- Backdrop -->
      <div
        class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
        @click="closeFormModal"
      ></div>

      <!-- Modal Content -->
      <div
        class="relative bg-white rounded-xl shadow-xl max-h-[95vh] w-full max-w-lg overflow-hidden transform transition-all"
      >
        <!-- Modal Header -->
        <div
          class="px-6 py-2 border-b border-gray-100 flex justify-between items-start"
        >
          <div>
            <h3 class="text-xl font-bold text-gray-900 mt-2">
              {{ isEditMode ? "Edit Category" : "Add Category" }}
            </h3>
            <p class="text-gray-500 text-sm mt-1">
              {{
                isEditMode
                  ? "Update existing category details."
                  : "Add a new category to your list."
              }}
            </p>
          </div>
          <button
            @click="closeFormModal"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              ></path>
            </svg>
          </button>
        </div>

        <!-- Modal Body -->

        <div class="px-6 py-2 overflow-y-auto">
          <form @submit.prevent="saveCategory">
            <!-- Category Name -->
            <div class="mb-5">
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Category Name <span class="text-red-500">*</span>
              </label>

              <input
                v-model="formData.name"
                type="text"
                required
                placeholder="Enter category name"
                class="w-full rounded-xl border border-slate-300 bg-white px-2 py-2 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
              />
            </div>

            <!-- Category Logo -->
            <div class="mb-2">
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Category Logo
              </label>

              <div class="flex flex-col items-center gap-3">
                <!-- Logo Preview -->
                <div
                  class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-100"
                >
                  <img
                    v-if="displayedLogo()"
                    :src="displayedLogo()!"
                    alt="Category logo"
                    class="h-full w-full object-cover"
                  />

                  <Folder v-else class="h-10 w-10 text-slate-400" />
                </div>

                <!-- Hidden File Input -->
                <input
                  ref="logoInput"
                  type="file"
                  accept="image/png, image/jpeg, image/jpg, image/gif"
                  class="hidden"
                  @change="handleLogoChange"
                />

                <!-- Upload and Remove Buttons -->
                <div class="flex flex-wrap justify-center gap-2">
                  <button
                    type="button"
                    @click="openLogoPicker"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                  >
                    <Upload class="h-4 w-4" />
                    Upload Logo
                  </button>

                  <button
                    v-if="displayedLogo()"
                    type="button"
                    @click="removeLogo"
                    class="inline-flex items-center gap-2 rounded-xl border border-red-200 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50"
                  >
                    <Trash2 class="h-4 w-4" />
                    Remove
                  </button>
                </div>
              </div>

              <p class="mt-2 text-center text-xs text-slate-400">
                JPG, PNG or GIF. Maximum size 2 MB.
              </p>
            </div>
          </form>
        </div>

        <!-- Modal Footer -->
        <div
          class="px-6 py-4 border-t border-gray-100 flex justify-end space-x-3 bg-gray-50"
        >
          <button
            @click="closeFormModal"
            type="button"
            class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="saveCategory"
            type="button"
            class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"
          >
            {{ isEditMode ? "Save Changes" : "Save Category" }}
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- </div> -->
</template>
