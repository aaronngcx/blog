<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import MyEditor from "@/Components/MyEditor.vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    post: Object,
});

const form = useForm({
    title: props.post.title,
    content: props.post.content,
    url_slug: props.post.url_slug,
    meta_description: props.post.meta_description,
    tags: props.post.tags || [],
    media: [],
});

const editingTagIndex = ref(null);

const updateSlug = (event) => {
    const inputValue = event.target.value;
    form.url_slug = formatSlug(inputValue);
};

function formatSlug(text) {
    return text
        .trim()
        .toLowerCase()
        .replace(/\s+/g, "-")
        .replace(/[^\w-]+/g, "")
        .replace(/--+/g, "-")
        .replace(/^-+|-+$/g, "");
}

function addTag(tag) {
    if (tag && !form.tags.some((t) => t.name === tag)) {
        form.tags.push({ name: tag });
    }
}

function removeTag(index) {
    form.tags.splice(index, 1);
}

function editTag(index) {
    editingTagIndex.value = index;
}

function updateTag(index, newTagName) {
    if (
        newTagName &&
        !form.tags.some((t, i) => i !== index && t.name === newTagName)
    ) {
        form.tags[index].name = newTagName;
        editingTagIndex.value = null; // Reset editing state
    }
}

watch(
    () => form.title,
    (newTitle) => {
        form.url_slug = formatSlug(newTitle);
    }
);

const handleFileUpload = (event) => {
    const files = event.target.files;
    for (let i = 0; i < files.length; i++) {
        form.media.push(files[i]);
    }
};

const removeMedia = (index) => {
    form.media.splice(index, 1);
};

function preventEnter(event) {
    if (event.key === "Enter") {
        event.preventDefault();
    }
}

function submit() {
    const formData = new FormData();

    Object.keys(form).forEach((key) => {
        if (key === "media") {
            form[key].forEach((file, index) => {
                formData.append(`media[${index}]`, file);
            });
        } else {
            formData.append(key, form[key]);
        }
    });

    form.post(route("posts.update", props.post.id), formData, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset();
        },
    });
}
</script>

<template>
    <AppLayout title="Edit Post">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Post
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <form @submit.prevent="submit">
                <div class="grid gap-6">
                    <!-- Title Input -->
                    <div>
                        <label
                            for="title"
                            class="block text-sm font-medium text-gray-700"
                            >Title</label
                        >
                        <input
                            v-model="form.title"
                            type="text"
                            id="title"
                            @keypress="preventEnter"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            required
                        />
                        <span
                            v-if="form.errors.title"
                            class="text-red-500 text-sm"
                            >{{ form.errors.title }}</span
                        >
                    </div>

                    <!-- Content Input -->
                    <div>
                        <label
                            for="content"
                            class="block text-sm font-medium text-gray-700"
                            >Content</label
                        >
                    </div>
                    <MyEditor v-model="form.content" />

                    <!-- URL Slug Input -->
                    <div>
                        <label
                            for="url_slug"
                            class="block text-sm font-medium text-gray-700"
                            >URL Slug</label
                        >
                        <input
                            v-model="form.url_slug"
                            type="text"
                            id="url_slug"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            @blur="updateSlug"
                            @keypress="preventEnter"
                        />
                        <span
                            v-if="form.errors.url_slug"
                            class="text-red-500 text-sm"
                            >{{ form.errors.url_slug }}</span
                        >
                    </div>

                    <!-- Meta Description Input -->
                    <div>
                        <label
                            for="meta_description"
                            class="block text-sm font-medium text-gray-700"
                            >Meta Description</label
                        >
                        <input
                            v-model="form.meta_description"
                            type="text"
                            id="meta_description"
                            @keypress="preventEnter"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        />
                        <span
                            v-if="form.errors.meta_description"
                            class="text-red-500 text-sm"
                            >{{ form.errors.meta_description }}</span
                        >
                    </div>

                    <div>
                        <label
                            for="state"
                            class="block text-sm font-medium text-gray-700"
                            >State</label
                        >
                        <select
                            v-model="form.state"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="" disabled>Select a state</option>
                            <option
                                v-for="state in states"
                                :key="state.id"
                                :value="state.id"
                            >
                                {{ state.name }}
                            </option>
                        </select>
                        <span
                            v-if="form.errors.state"
                            class="text-red-500 text-sm"
                            >{{ form.errors.state }}</span
                        >
                    </div>

                    <div>
                        <label
                            for="status"
                            class="block text-sm font-medium text-gray-700"
                            >Status</label
                        >
                        <select
                            v-model="form.status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="public">Public</option>
                            <option value="hidden">Hidden</option>
                        </select>
                        <span
                            v-if="form.errors.status"
                            class="text-red-500 text-sm"
                            >{{ form.errors.status }}</span
                        >
                    </div>

                    <div>
                        <label
                            for="media"
                            class="block text-sm font-medium text-gray-700"
                            >Upload Media</label
                        >
                        <input
                            type="file"
                            id="media"
                            @change="handleFileUpload"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            multiple
                        />
                        <div class="mt-2">
                            <span
                                v-for="(file, index) in form.media_uploads"
                                :key="index"
                                class="block text-sm text-gray-700"
                            >
                                {{ file.name }}
                                <button
                                    type="button"
                                    @click="removeMedia(index)"
                                    class="text-red-600 ml-2"
                                >
                                    &times;
                                </button>
                            </span>
                        </div>
                        <span
                            v-if="form.errors.media"
                            class="text-red-500 text-sm"
                            >{{ form.errors.media }}</span
                        >
                    </div>

                    <!-- Tags Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Tags</label
                        >
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span
                                v-for="(tag, index) in form.tags"
                                :key="index"
                                class="bg-blue-200 text-blue-800 rounded-full px-3 py-1 text-sm flex items-center"
                            >
                                <span v-if="editingTagIndex !== index">{{
                                    tag.name
                                }}</span>
                                <input
                                    v-if="editingTagIndex === index"
                                    type="text"
                                    v-model="tag.name"
                                    @blur="
                                        editingTagIndex = null;
                                        updateTag(index, tag.name);
                                    "
                                    @keyup.enter="updateTag(index, tag.name)"
                                    @keydown.enter.prevent
                                    class="border border-gray-300 rounded-md p-1"
                                />
                                <button
                                    type="button"
                                    @click="removeTag(index)"
                                    class="ml-2 text-red-600"
                                >
                                    &times;
                                </button>
                                <button
                                    v-if="editingTagIndex !== index"
                                    type="button"
                                    @click="editTag(index)"
                                    class="ml-2 text-blue-500"
                                >
                                    Edit
                                </button>
                            </span>
                        </div>
                        <input
                            type="text"
                            id="tags"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            placeholder="Add a tag and press Enter"
                            @keyup.enter="
                                addTag($event.target.value);
                                $event.target.value = '';
                            "
                            @keypress="preventEnter"
                        />
                        <span
                            v-if="form.errors.tags"
                            class="text-red-500 text-sm"
                            >{{ form.errors.tags }}</span
                        >
                    </div>

                    <!-- Update Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="btn btn-primary bg-blue-600 text-white p-2 rounded"
                        >
                            Update Post
                        </button>
                    </div>
                </div>
            </form>

            <SectionBorder />
        </div>
    </AppLayout>
</template>

<style>
.tiptap h1 {
    font-size: 32px;
    font-weight: bold;
}

.tiptap h2 {
    font-size: 16px;
    font-weight: bold;
}

.tiptap h3 {
    font-size: 12px;
    font-weight: bold;
}
</style>
