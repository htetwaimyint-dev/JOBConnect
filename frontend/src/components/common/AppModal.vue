<script setup lang="ts">
import { watch, onBeforeUnmount } from "vue";
import { X } from "lucide-vue-next";

const props = defineProps<{
    show: boolean;
    title: string;
    description?: string;
    maxWidth?: string;
}>();

const emit = defineEmits<{
    close: [];
}>();

const closeModal = () => {
    emit("close");
};

watch(
    () => props.show,
    (isOpen) => {
        document.body.style.overflow = isOpen ? "hidden" : "";
    },
);

onBeforeUnmount(() => {
    document.body.style.overflow = "";
});
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="show"
                class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4 backdrop-blur-sm"
                @click.self="closeModal" @keydown.esc="closeModal">
                <div class="relative my-auto w-full overflow-hidden rounded-2xl bg-white shadow-2xl"
                    :class="maxWidth || 'max-w-2xl'" role="dialog" aria-modal="true" :aria-label="title">
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">
                                {{ title }}
                            </h2>

                            <p v-if="description" class="mt-1 text-sm text-slate-500">
                                {{ description }}
                            </p>
                        </div>

                        <button type="button" aria-label="Close modal"
                            class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                            @click="closeModal">
                            <X :size="20" />
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="max-h-[75vh] overflow-y-auto p-6">
                        <slot />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease;
}

.modal-enter-active>div,
.modal-leave-active>div {
    transition: transform 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from>div,
.modal-leave-to>div {
    transform: translateY(15px) scale(0.98);
}
</style>
