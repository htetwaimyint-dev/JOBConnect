import api from "@/services/api";

import type {
  Category,
  CategoryListParams,
  CategoryListResponse,
  CategoryResponse,
  PaginatedResponse,
} from "../types/category.types";

const categoryRepository = {
  // Get all categories
  async getCategories(
    params?: CategoryListParams,
  ): Promise<PaginatedResponse<Category>> {
    const response = await api.get<PaginatedResponse<Category>>("/categories", {
      params,
    });

    return response.data;
  },

  // Get category by ID
  async getCategoryById(id: number): Promise<CategoryResponse> {
    const response = await api.get<CategoryResponse>(`/categories/${id}`);
    return response.data;
  },

  // Create category
  async createCategory(data: FormData): Promise<CategoryResponse> {
    const response = await api.post<CategoryResponse>("/categories", data, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    return response.data;
  },

  // Update category
  async updateCategory(id: number, data: FormData): Promise<CategoryResponse> {
    const response = await api.post<CategoryResponse>(
      `/categories/${id}`,
      data,
      {
        headers: { "Content-Type": "multipart/form-data" },
      },
    );

    return response.data;
  },

  // Delete category
  async deleteCategory(id: number): Promise<{ message: string }> {
    const response = await api.delete<{ message: string }>(`/categories/${id}`);

    return response.data;
  },

  async bulkDeleteCategories(ids: number[]) {
    const response = await api.delete("/categories/bulk-delete", {
      data: { ids },
    });

    return response.data;
  },

  async deleteAllUnusedCategories() {
    const response = await api.delete("/categories/delete-all");

    return response.data;
  },
};

export default categoryRepository;
