<script setup lang="ts">
import type { Company } from "../types/company.types";

defineProps<{
    company: Company;
}>();
</script>

<template>
    <div class="border border-gray-100 bg-white px-6 py-7 sm:px-8">
        <!-- About -->
        <section class="pb-7">
            <h2 class="text-lg font-bold text-gray-900">About Company</h2>

            <p class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-600">
                {{ company.description }}
            </p>
        </section>

        <!-- Divider -->
        <div class="border-t-4 border-gray-200"></div>

        <!-- Company Details -->
        <section class="py-7">
            <h2 class="text-lg font-bold text-gray-900">Company Details</h2>

            <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-medium text-gray-400">Industry</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">
                        {{ company.industry }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400">Location</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">
                        {{ company.location }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400">Founded</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">
                        {{ company.foundedYear }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-400">
                        Company Size
                    </p>
                    <p class="mt-1 text-sm font-semibold text-gray-800">
                        {{ company.employeeCount }} employees
                    </p>
                </div>
            </div>
        </section>

        <!-- Divider -->
        <div class="border-t-4 border-gray-200"></div>

        <!-- Benefits -->
        <section class="pt-7">
            <h2 class="text-lg font-bold text-gray-900">Benefits & Perks</h2>

            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div
                    v-for="benefit in company.benefits"
                    :key="benefit.id"
                    class="flex items-start gap-3 rounded-xl bg-gray-50 p-4"
                >
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-violet-100 text-lg"
                    >
                        {{ benefit.icon }}
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">
                            {{ benefit.title }}
                        </h3>

                        <p class="mt-1 text-xs leading-5 text-gray-500">
                            {{ benefit.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
