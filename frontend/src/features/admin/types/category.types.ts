export interface Category {
  id: number;
  name: string;
  logo: string | null;
  created_at: string;
  updated_at: string;
}

export interface PaginatedResponse<T> {
  current_page: number;
  data: T[];
  last_page: number;
  total: number;
  from: number | null;
  to: number | null;
  per_page: number;
}

export interface CategoryListParams {
  search?: string;
  page?: number;
  per_page?: number;
}

export interface CategoryListResponse {
  data: Category[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface CategoryResponse {
  data: Category;
}
