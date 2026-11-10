<script setup lang="ts">
import BaseButton from "@/components/common/BaseButton.vue";
</script>

<template>
    <header class="bg-[#0A1024] text-white border-b border-slate-800">
        <div
            class="mx-auto flex max-w-7xl items-center justify-between px-6 py-3.5"
        >
            <!-- Built In Brand Header -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <span
                        class="rounded bg-[#0052FF] px-2.5 py-1 text-sm font-black tracking-wide text-white"
                    >
                        builtin
                    </span>
                    <span
                        class="text-xs font-medium text-slate-300 border-l border-slate-700 pl-3"
                    >
                    </span>
                </div>
            </div>

            <!-- Right Action Items -->
            <div class="flex items-center gap-4">
                <BaseButton
                    type="button"
                    size="28"
                    variant="outline"
                    color="neutral"
                    class="border-slate-700 bg-transparent text-slate-200 hover:bg-slate-800 text-[11px] font-bold tracking-wider"
                >
                    VISIT HOMEPAGE
                </BaseButton>

                <!-- User Profile Badge -->
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-[#1E293B] border border-slate-700 text-xs font-bold text-white shadow-xs"
                >
                    <svg
                        class="h-4 w-4 text-slate-300"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                        />
                    </svg>
                </div>
            </div>
        </div>
    </header>
</template>
