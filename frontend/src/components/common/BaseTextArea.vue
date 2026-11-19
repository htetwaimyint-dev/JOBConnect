<script setup lang="ts">
import { watch } from "vue";
import { useEditor, EditorContent } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Placeholder from "@tiptap/extension-placeholder";

const props = withDefaults(
    defineProps<{
        label?: string;
        placeholder?: string;
        error?: string;
        minHeight?: string;
    }>(),
    {
        label: "",
        placeholder: "Start writing...",
        error: "",
        minHeight: "180px",
    },
);

const content = defineModel<string>({ required: true });

const editor = useEditor({
    content: content.value || "",

    extensions: [
        StarterKit,
        Placeholder.configure({
            placeholder: props.placeholder,
        }),
    ],

    editorProps: {
        attributes: {
            class: "tiptap-content focus:outline-none",
            "aria-label": props.label || "Rich text editor",
        },
    },

    onUpdate: ({ editor }) => {
        content.value = editor.getHTML();
    },
});

// Keep editor content synchronized if the parent changes the model.
watch(
    () => content.value,
    (newContent) => {
        const instance = editor.value;

        if (instance && instance.getHTML() !== (newContent || "")) {
            instance.commands.setContent(newContent || "", {
                emitUpdate: false,
            });
        }
    },
);
</script>

<template>
    <div class="space-y-2">
        <label v-if="label" class="block text-sm font-medium text-gray-900">
            {{ label }}
        </label>

        <div
            class="overflow-hidden rounded-md border bg-white transition"
            :class="
                error
                    ? 'border-red-500'
                    : 'border-gray-300 focus-within:border-purple-500 focus-within:ring-2 focus-within:ring-purple-500/20'
            "
        >
            <!-- Toolbar -->
            <div
                v-if="editor"
                class="flex flex-wrap items-center gap-1 border-b border-gray-200 bg-gray-50 p-2 text-xs"
            >
                <!-- Bold -->
                <button
                    type="button"
                    title="Bold"
                    aria-label="Bold"
                    :disabled="!editor.can().chain().focus().toggleBold().run()"
                    :class="{
                        'bg-purple-100 text-purple-700':
                            editor.isActive('bold'),
                    }"
                    class="rounded-md p-2 text-gray-600 hover:bg-gray-200 disabled:opacity-40"
                    @click="editor.chain().focus().toggleBold().run()"
                >
                    <span class="font-bold">B</span>
                </button>

                <!-- Italic -->
                <button
                    type="button"
                    title="Italic"
                    aria-label="Italic"
                    :class="{
                        'bg-purple-100 text-purple-700':
                            editor.isActive('italic'),
                    }"
                    class="rounded-md p-2 text-gray-600 hover:bg-gray-200"
                    @click="editor.chain().focus().toggleItalic().run()"
                >
                    <span class="italic">I</span>
                </button>

                <!-- Underline -->
                <button
                    type="button"
                    title="Underline"
                    aria-label="Underline"
                    :class="{
                        'bg-purple-100 text-purple-700':
                            editor.isActive('underline'),
                    }"
                    class="rounded-md p-2 text-gray-600 hover:bg-gray-200"
                    @click="editor.chain().focus().toggleUnderline().run()"
                >
                    <span class="underline">U</span>
                </button>

                <div class="mx-1 h-6 w-px bg-gray-300" />

                <!-- Headings -->
                <button
                    type="button"
                    title="Heading 2"
                    :class="{
                        'bg-purple-100 text-purple-700': editor.isActive(
                            'heading',
                            { level: 2 },
                        ),
                    }"
                    class="rounded-md px-2 py-2 font-bold text-gray-600 hover:bg-gray-200"
                    @click="
                        editor.chain().focus().toggleHeading({ level: 2 }).run()
                    "
                >
                    H2
                </button>

                <button
                    type="button"
                    title="Heading 3"
                    :class="{
                        'bg-purple-100 text-purple-700': editor.isActive(
                            'heading',
                            { level: 3 },
                        ),
                    }"
                    class="rounded-md px-2 py-2 font-bold text-gray-600 hover:bg-gray-200"
                    @click="
                        editor.chain().focus().toggleHeading({ level: 3 }).run()
                    "
                >
                    H3
                </button>

                <div class="mx-1 h-6 w-px bg-gray-300" />

                <!-- Lists -->
                <button
                    type="button"
                    title="Bullet List"
                    aria-label="Bullet list"
                    :class="{
                        'bg-purple-100 text-purple-700':
                            editor.isActive('bulletList'),
                    }"
                    class="rounded-md p-2 text-gray-600 hover:bg-gray-200"
                    @click="editor.chain().focus().toggleBulletList().run()"
                >
                    <span>☷</span>
                </button>

                <button
                    type="button"
                    title="Numbered List"
                    aria-label="Numbered list"
                    :class="{
                        'bg-purple-100 text-purple-700':
                            editor.isActive('orderedList'),
                    }"
                    class="rounded-md p-2 text-gray-600 hover:bg-gray-200"
                    @click="editor.chain().focus().toggleOrderedList().run()"
                >
                    <span class="font-semibold">1.</span>
                </button>

                <button
                    type="button"
                    title="Blockquote"
                    aria-label="Blockquote"
                    :class="{
                        'bg-purple-100 text-purple-700':
                            editor.isActive('blockquote'),
                    }"
                    class="rounded-md p-2 text-gray-600 hover:bg-gray-200"
                    @click="editor.chain().focus().toggleBlockquote().run()"
                >
                    <span>❝</span>
                </button>

                <div class="mx-1 h-6 w-px bg-gray-300" />

                <!-- Undo / Redo -->
                <button
                    type="button"
                    title="Undo"
                    aria-label="Undo"
                    :disabled="!editor.can().undo()"
                    class="rounded-md p-2 text-gray-600 hover:bg-gray-200 disabled:opacity-40"
                    @click="editor.chain().focus().undo().run()"
                >
                    ↶
                </button>

                <button
                    type="button"
                    title="Redo"
                    aria-label="Redo"
                    :disabled="!editor.can().redo()"
                    class="rounded-md p-2 text-gray-600 hover:bg-gray-200 disabled:opacity-40"
                    @click="editor.chain().focus().redo().run()"
                >
                    ↷
                </button>
            </div>

            <!-- Editor -->
            <EditorContent
                :editor="editor"
                class="rich-text-editor p-4 text-sm text-gray-900"
                :style="{ minHeight }"
            />
        </div>

        <!-- Validation error -->
        <p v-if="error" class="text-sm text-red-500">
            {{ error }}
        </p>
    </div>
</template>

<style>
.rich-text-editor .tiptap {
    min-height: inherit;
    outline: none;
}

.rich-text-editor .tiptap p {
    margin-bottom: 0.5rem;
}

.rich-text-editor .tiptap h2 {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0.75rem 0;
}

.rich-text-editor .tiptap h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0.75rem 0;
}

.rich-text-editor .tiptap ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin: 0.5rem 0;
}

.rich-text-editor .tiptap ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin: 0.5rem 0;
}

.rich-text-editor .tiptap blockquote {
    border-left: 3px solid #a855f7;
    padding-left: 1rem;
    margin: 0.75rem 0;
    color: #6b7280;
}

.rich-text-editor .tiptap p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    color: #9ca3af;
    float: left;
    height: 0;
    pointer-events: none;
}

.rich-text-editor .tiptap p.is-editor-empty:first-child::before {
    color: #9ca3af;
    content: attr(data-placeholder);
    float: left;
    height: 0;
    pointer-events: none;
}
</style>
