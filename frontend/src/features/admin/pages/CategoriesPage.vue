<script setup lang="ts">
import { ref, reactive, computed, onMounted, onBeforeUnmount } from "vue";
import categoryRepository from "../repositories/categoryRepository";
import {
  Upload,
  Trash2,
  Folder,
  Search,
  Plus,
  SquarePen,
  X,
  ChevronLeft,
  ChevronRight,
  LoaderCircle,
} from "lucide-vue-next";

// Category Data

const categories = ref<any[]>([]);
const isLoading = ref(false);
const isDeleting = ref(false);

const searchQuery = ref("");
const perPage = ref(10);

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

// Search with debounce
const handleSearch = () => {
  // Clear previous timer
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  // Wait 400ms before sending API request
  searchTimeout = setTimeout(() => {
    clearSelection();
    getCategories(1);
  }, 400);
};

// Pagination data from Laravel
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

// Bulk Selection

// Store selected IDs from ALL pages
const selectedIds = ref<number[]>([]);

// Only categories without jobs can be selected
const selectableCategories = computed(() => {
  return categories.value.filter(
    (category) => Number(category.jobs_count ?? 0) === 0,
  );
});

// Total selected categories across all pages
const selectedCount = computed(() => selectedIds.value.length);

// Check whether all deletable categories on CURRENT page are selected
const isAllSelected = computed(() => {
  return (
    selectableCategories.value.length > 0 &&
    selectableCategories.value.every((category) =>
      selectedIds.value.includes(category.id),
    )
  );
});

// Select / deselect all deletable categories on current page
const toggleSelectAll = (checked: boolean) => {
  const currentPageIds = selectableCategories.value.map(
    (category) => category.id,
  );

  if (checked) {
    selectedIds.value = [...new Set([...selectedIds.value, ...currentPageIds])];
  } else {
    selectedIds.value = selectedIds.value.filter(
      (id) => !currentPageIds.includes(id),
    );
  }
};

// Select / deselect one category
const toggleSelect = (id: number, checked: boolean) => {
  const category = categories.value.find((item) => item.id === id);

  // Cannot select a category that has jobs
  if (!category || Number(category.jobs_count ?? 0) > 0) {
    return;
  }

  if (checked) {
    if (!selectedIds.value.includes(id)) {
      selectedIds.value.push(id);
    }
  } else {
    selectedIds.value = selectedIds.value.filter(
      (selectedId) => selectedId !== id,
    );
  }
};

// Clear all selected IDs
const clearSelection = () => {
  selectedIds.value = [];
};

// Pagination

const getCategories = async (page = 1) => {
  isLoading.value = true;

  try {
    const result = await categoryRepository.getCategories({
      page,
      search: searchQuery.value.trim() || undefined,
      per_page: perPage.value,
    });

    categories.value = result.data;

    pagination.current_page = result.current_page;
    pagination.last_page = result.last_page;
    pagination.total = result.total;
    pagination.from = result.from || 0;
    pagination.to = result.to || 0;
  } catch (error) {
    console.error("API Error:", error);
    alert("Failed to load categories.");
  } finally {
    isLoading.value = false;
  }
};

// Change number of entries
const handlePerPageChange = () => {
  getCategories(1);
};

// Change page
const changePage = (page: number) => {
  if (
    page >= 1 &&
    page <= pagination.last_page &&
    page !== pagination.current_page
  ) {
    getCategories(page);
  }
};

// Calculate the correct page after deleting categories
const getTargetPage = (deletedCount: number) => {
  const remainingTotal = Math.max(0, pagination.total - deletedCount);

  const newLastPage = Math.max(1, Math.ceil(remainingTotal / perPage.value));

  return Math.min(pagination.current_page, newLastPage);
};

// Individual Delete

const handleDelete = async (id: number) => {
  if (!confirm("Are you sure you want to delete this category?")) {
    return;
  }

  try {
    const targetPage = getTargetPage(1);

    await categoryRepository.deleteCategory(id);

    // Remove ID from selected list
    clearSelection();

    // Refresh correct page
    await getCategories(targetPage);
  } catch (error: any) {
    console.error("Delete Error:", error.response || error);

    const message =
      error.response?.data?.message || "Failed to delete category.";

    alert(message);
  }
};

// Bulk Delete

const handleBulkDelete = async () => {
  if (selectedIds.value.length === 0) {
    alert("Please select at least one category.");
    return;
  }

  const idsToDelete = [...selectedIds.value];

  const confirmed = confirm(
    `Are you sure you want to delete ${idsToDelete.length} selected categories?`,
  );

  if (!confirmed) {
    return;
  }

  isDeleting.value = true;

  try {
    // Calculate page before deleting
    const targetPage = getTargetPage(idsToDelete.length);

    // Send all selected IDs to Laravel
    await categoryRepository.bulkDeleteCategories(idsToDelete);

    // Clear selection after successful deletion
    clearSelection();

    // Refresh category list
    await getCategories(targetPage);

    alert(`${idsToDelete.length} categories deleted successfully.`);
  } catch (error: any) {
    console.error("Bulk Delete Error:", error.response || error);

    const message =
      error.response?.data?.message || "Failed to delete selected categories.";

    alert(message);
  } finally {
    isDeleting.value = false;
  }
};

// Add / Edit Modal

const isFormModalOpen = ref(false);
const isEditMode = ref(false);
const currentEditId = ref<number | null>(null);

const formData = reactive({
  name: "",
  logo: null as File | string | null,
});

const logoInput = ref<HTMLInputElement | null>(null);
const previewLogo = ref<string | null>(null);
const isLogoRemoved = ref(false);

const isViewModalOpen = ref(false);
const selectedCategory = ref<any | null>(null);

const openLogoPicker = () => {
  logoInput.value?.click();
};

const handleLogoChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  if (!file) return;

  if (file.size > 2 * 1024 * 1024) {
    alert("Image size must not exceed 2 MB.");
    target.value = "";
    return;
  }

  if (previewLogo.value) {
    URL.revokeObjectURL(previewLogo.value);
  }

  formData.logo = file;
  isLogoRemoved.value = false;
  previewLogo.value = URL.createObjectURL(file);
};

const getImageUrl = (path: string) => {
  if (path.startsWith("http://") || path.startsWith("https://")) {
    return path;
  }

  return `http://localhost:8000/storage/${path.replace(/^\/+/, "")}`;
};

const displayedLogo = () => {
  if (previewLogo.value) {
    return previewLogo.value;
  }

  if (typeof formData.logo === "string" && formData.logo) {
    return getImageUrl(formData.logo);
  }

  return null;
};

const removeLogo = () => {
  formData.logo = null;
  previewLogo.value = null;
  isLogoRemoved.value = true;

  if (logoInput.value) {
    logoInput.value.value = "";
  }
};

const openFormModal = (mode: "add" | "edit", category: any = null) => {
  isFormModalOpen.value = true;
  isLogoRemoved.value = false;

  if (previewLogo.value) {
    URL.revokeObjectURL(previewLogo.value);
  }

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

  if (previewLogo.value) {
    URL.revokeObjectURL(previewLogo.value);
  }

  previewLogo.value = null;
  formData.name = "";
  formData.logo = null;
  isLogoRemoved.value = false;

  if (logoInput.value) {
    logoInput.value.value = "";
  }
};

// Save Category

const saveCategory = async () => {
  if (!formData.name.trim()) {
    alert("Category Name is required.");
    return;
  }

  try {
    const data = new FormData();

    data.append("name", formData.name.trim());

    if (formData.logo instanceof File) {
      data.append("logo", formData.logo);
    } else if (isLogoRemoved.value) {
      data.append("remove_logo", "1");
    }

    if (isEditMode.value && currentEditId.value !== null) {
      data.append("_method", "PUT");

      await categoryRepository.updateCategory(currentEditId.value, data);

      await getCategories(pagination.current_page);
    } else {
      await categoryRepository.createCategory(data);

      // Return to first page after creating a category
      await getCategories(1);
    }

    clearSelection();
    closeFormModal();
  } catch (error: any) {
    console.error("Save Error:", error.response || error);

    const message = error.response?.data?.message || "Failed to save category.";

    alert(message);
  }
};

// View Modal

const closeViewModal = () => {
  isViewModalOpen.value = false;
  selectedCategory.value = null;
};

// Lifecycle

onMounted(() => {
  getCategories();
});

onBeforeUnmount(() => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  if (previewLogo.value) {
    URL.revokeObjectURL(previewLogo.value);
  }
});
</script>

<template>
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
        <div class="relative w-full max-w-md">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3">
            <Search class="w-5 h-5 text-gray-400" />
          </span>

          <input
            v-model="searchQuery"
            @input="handleSearch"
            type="text"
            placeholder="Search categories..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
          />
        </div>

        <button
          @click="openFormModal('add')"
          class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors"
        >
          <Plus class="w-4 h-4 mr-2" />
          Add Category
        </button>
      </div>
    </div>

    <!-- Selection Status & Bulk Delete -->
    <div class="px-6 py-2 flex justify-between items-center">
      <span class="text-sm text-gray-500">
        {{ selectedCount }} categories selected
      </span>

      <button
        @click="handleBulkDelete"
        :disabled="selectedCount === 0 || isDeleting"
        class="flex items-center gap-2 text-red-500 bg-red-50 hover:bg-red-100 font-medium py-1.5 px-3 rounded-md text-sm transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
      >
        <Trash2 class="w-4 h-4" />
        {{ isDeleting ? "Deleting..." : "Delete Selected" }}
      </button>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="py-16 text-center text-sm text-gray-500">
      Loading categories...
    </div>

    <!-- Category Table -->
    <div v-else-if="categories.length > 0">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-gray-50 border-b border-gray-200 text-gray-700 text-sm"
            >
              <th class="py-3 px-4"></th>
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
              <!-- Checkbox -->
              <td class="py-4 px-4">
                <input
                  type="checkbox"
                  :checked="selectedIds.includes(category.id)"
                  @change="
                    toggleSelect(
                      category.id,
                      ($event.target as HTMLInputElement).checked,
                    )
                  "
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
              </td>

              <!-- Correct Serial Number -->
              <td class="py-4 px-4 text-gray-600">
                {{ pagination.from + index }}
              </td>

              <!-- Logo -->
              <td class="py-3 px-4 font-medium text-center">
                <img
                  v-if="category.logo"
                  :src="getImageUrl(category.logo)"
                  :alt="category.name"
                  class="w-10 h-10 object-cover rounded-lg border border-gray-200"
                />

                <Folder v-else class="w-6 h-6 text-gray-400" />
              </td>

              <!-- Name -->
              <td class="py-4 px-4 font-medium text-gray-800">
                {{ category.name }}
              </td>

              <!-- Options -->
              <td class="py-4 px-4">
                <div class="flex space-x-3 text-lg">
                  <button
                    @click="openFormModal('edit', category)"
                    class="text-blue-500 hover:text-blue-700"
                    title="Edit"
                  >
                    <SquarePen class="w-5 h-5" />
                  </button>

                  <button
                    @click="handleDelete(category.id)"
                    class="text-red-500 hover:text-red-700"
                    title="Delete"
                  >
                    <Trash2 class="w-5 h-5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div
        class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50 rounded-b-xl"
      >
        <!-- Showing Info + Entries Selector -->
        <div class="flex items-center space-x-3 text-sm text-gray-500">
          <span>
            Showing
            <span class="font-medium text-gray-700">
              {{ pagination.from }}
            </span>
            –
            <span class="font-medium text-gray-700">
              {{ pagination.to }}
            </span>
            of
            <span class="font-medium text-gray-700">
              {{ pagination.total }}
            </span>
          </span>

          <span class="text-gray-300">|</span>

          <div class="flex items-center space-x-1.5">
            <span>Show</span>

            <select
              v-model.number="perPage"
              @change="handlePerPageChange"
              class="px-2.5 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-700 font-medium hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all cursor-pointer"
            >
              <option :value="5">5</option>
              <option :value="10">10</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
            </select>

            <span>entries</span>
          </div>
        </div>

        <!-- Pagination Controls -->
        <div class="flex items-center space-x-2">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1"
          >
            <ChevronLeft class="w-4 h-4" />
            Previous
          </button>

          <button
            v-for="page in pagination.last_page"
            :key="page"
            @click="changePage(page)"
            :class="
              page === pagination.current_page
                ? 'px-3 py-1 text-sm border border-blue-500 rounded-md bg-blue-50 text-blue-600 font-medium'
                : 'px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-600 hover:bg-gray-50 transition-colors'
            "
          >
            {{ page }}
          </button>

          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1"
          >
            Next
            <ChevronRight class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="py-16 px-6 text-center bg-white rounded-b-xl border-t border-gray-100"
    >
      <div v-if="searchQuery.trim()">
        <div
          class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400"
        >
          <Folder class="h-6 w-6" />
        </div>

        <h3 class="text-base font-semibold text-gray-800">
          No matching categories
        </h3>

        <p class="mt-1 text-sm text-gray-500">
          No categories matched "{{ searchQuery }}". Try another search term.
        </p>

        <button
          @click="
            searchQuery = '';
            clearSelection();
            getCategories(1);
          "
          class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
        >
          Clear Search
        </button>
      </div>

      <div v-else>
        <div
          class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600"
        >
          <Folder class="h-6 w-6" />
        </div>

        <h3 class="text-base font-semibold text-gray-800">No categories yet</h3>

        <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto">
          Your category list is currently empty. Add your first category to
          start organizing your items.
        </p>

        <button
          @click="openFormModal('add')"
          class="mt-5 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition-colors"
        >
          + Add Category
        </button>
      </div>
    </div>

    <!-- ADD / EDIT MODAL -->
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
        <!-- Header -->
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
            title="Close"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-2 max-h-[70vh] overflow-y-auto">
          <form @submit.prevent="saveCategory">
            <!-- Category Name -->
            <div class="mb-5">
              <label class="mb-2 block text-sm font-medium text-slate-700">
                Category Name
                <span class="text-red-500">*</span>
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

                <!-- File Input -->
                <input
                  ref="logoInput"
                  type="file"
                  accept="image/png,image/jpeg,image/jpg,image/gif"
                  class="hidden"
                  @change="handleLogoChange"
                />

                <!-- Buttons -->
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
</template>
