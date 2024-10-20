<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import MyEditor from "@/Components/MyEditor.vue";

const props = defineProps({
    states: Array,
});

const form = useForm({
    title: "",
    content: "",
    url_slug: "",
    meta_description: "",
    state: "",
    status: "",
    tags: [],
    media: [],
});

function addTag(tag) {
    if (tag && !form.tags.includes(tag)) {
        form.tags.push(tag);
    }
}

function removeTag(tagToRemove) {
    f;
    form.tags = form.tags.filter((tag) => tag !== tagToRemove);
}

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

function handleFileUpload(event) {
    form.media = Array.from(event.target.files);
}

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

    form.post(route("posts.store"), {
        data: formData,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });
}
</script>

<template>
    <AppLayout title="Create Post">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New Post
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <form @submit.prevent="submit">
                <div class="grid gap-6">
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
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            required
                            @keypress="preventEnter"
                        />
                        <span
                            v-if="form.errors.title"
                            class="text-red-500 text-sm"
                            >{{ form.errors.title }}</span
                        >
                    </div>

                    <div>
                        <label
                            for="content"
                            class="block text-sm font-medium text-gray-700"
                            >Content</label
                        >
                    </div>
                    <MyEditor v-model="form.content" />

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
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            @keypress="preventEnter"
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
                        <select v-model="form.state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                            <select v-model="form.status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                                v-for="(file, index) in form.media"
                                :key="index"
                                class="block text-sm text-gray-700"
                            >
                                {{ file.name }}
                            </span>
                        </div>
                        <span
                            v-if="form.errors.media"
                            class="text-red-500 text-sm"
                            >{{ form.errors.media }}</span
                        >
                    </div>

                    <div>
                        <label
                            for="tags"
                            class="block text-sm font-medium text-gray-700"
                            >Tags</label
                        >
                        <div class="flex items-center mb-2">
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
                        </div>
                        <p class="text-xs italic text-gray-500">
                            Add a tag and press Enter
                        </p>

                        <div class="flex flex-wrap">
                            <span
                                v-for="(tag, index) in form.tags"
                                :key="index"
                                class="bg-blue-200 text-blue-800 rounded-full px-2 py-1 text-sm mr-2 mb-2 flex items-center"
                            >
                                {{ tag }}
                                <button
                                    type="button"
                                    @click="removeTag(index)"
                                    class="ml-1 text-red-600"
                                >
                                    x
                                </button>
                            </span>
                        </div>
                        <span
                            v-if="form.errors.tags"
                            class="text-red-500 text-sm"
                            >{{ form.errors.tags }}</span
                        >
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="btn btn-primary bg-blue-600 text-white p-2 rounded"
                        >
                            Create Post
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
    font-size: 16;
    font-weight: bold;
}

.tiptap h3 {
    font-size: 12;
    font-weight: bold;
}
</style>
