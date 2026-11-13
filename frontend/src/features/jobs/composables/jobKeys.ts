export const jobKeys = {
    all: ["employer-jobs"] as const,

    lists: () => [...jobKeys.all, "list"] as const,

    list: (page: number) => [...jobKeys.lists(), page] as const,

    details: () => [...jobKeys.all, "detail"] as const,

    detail: (id: number) => [...jobKeys.details(), id] as const,
};
