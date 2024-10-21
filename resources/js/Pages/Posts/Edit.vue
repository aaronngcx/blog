<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import MyEditor from "@/Components/MyEditor.vue";
import { usePage } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    post: Object,
    states: Array,
    allTags: Array,
});

const form = useForm({
    title: props.post.title,
    content: props.post.content,
    url_slug: props.post.url_slug,
    meta_description: props.post.meta_description,
    state_id: props.post.state_id || "",
    status: props.post.status,
    media: props.post.media_uploads || [],
    tags: props.post.tags.map((tag) => tag.name),
});

const filteredTags = ref([]);
const tagInput = ref("");
const previewLink = ref(null);

const previewPost = async () => {
    try {
        const response = await axios.post(
            route("posts.generate-preview-link", { post: props.post.id }),
            {
                postId: props.post.id,
            }
        );
        const link = response.data.preview_link;
        window.open(link, "_blank");
    } catch (error) {
        console.error("Error generating preview link:", error);
    }
};

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

const addTag = (tag) => {
    const trimmedTag = tag.trim();
    if (trimmedTag && !form.tags.includes(trimmedTag)) {
        form.tags.push(trimmedTag);
        filteredTags.value = [];
        tagInput.value = "";
    }
};

const filterTags = (input) => {
    const trimmedInput = input.trim().toLowerCase();
    filteredTags.value = props.allTags.filter((tag) =>
        tag.name.toLowerCase().includes(trimmedInput)
    );
};

const removeTag = (index) => {
    form.tags.splice(index, 1);
};

function editTag(index) {
    editingTagIndex.value = index;
}

function updateTag(index, newTagName) {
    if (
        newTagName &&
        !form.tags.some((t, i) => i !== index && t.name === newTagName)
    ) {
        form.tags[index].name = newTagName;
        editingTagIndex.value = null;
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

const removeMedia = async (index) => {
    const mediaId = form.media[index].id;
    const confirmed = confirm("Are you sure you want to delete this media?");

    if (!confirmed) {
        return;
    }

    try {
        await axios.delete(
            route("posts.delete-media", { post: props.post.id, mediaId })
        );
        form.media.splice(index, 1);
    } catch (error) {
        console.error("Error deleting media:", error);
    }
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

            <div class="flex justify-end mt-4 space-x-4">
                <button
                    @click="previewPost"
                    class="bg-yellow-600 text-white py-2 px-4 rounded-md hover:bg-yellow-500"
                >
                    Preview Post
                </button>
            </div>
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
                            v-model="form.state_id"
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
                            v-if="form.errors.state_id"
                            class="text-red-500 text-sm"
                            >{{ form.errors.state_id }}</span
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
                        >
                            Upload Media
                        </label>
                        <input
                            type="file"
                            id="media"
                            @change="handleFileUpload"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            multiple
                        />

                        <div class="mt-2">
                            <div class="flex" v-if="form.media.length">
                                <span
                                    v-for="(file, index) in form.media"
                                    :key="index"
                                    class="block text-sm text-gray-700"
                                >
                                    <template v-if="file.file_path">
                                        <img
                                            :src="file.file_path"
                                            alt="Media preview"
                                            class="w-48 h-48 mx-5 object-cover"
                                        />
                                        <button
                                            type="button"
                                            @click="removeMedia(index)"
                                            class="text-red-600 mt-2 p-2 rounded-md hover:bg-red-100"
                                        >
                                            &times;
                                        </button>
                                    </template>

                                    <template v-else>
                                        {{ file.name }}
                                        <button
                                            type="button"
                                            @click="removeMedia(index)"
                                            class="text-red-600 mt-2"
                                        >
                                            &times;
                                        </button>
                                    </template>
                                </span>
                            </div>
                        </div>

                        <span
                            v-if="form.errors.media"
                            class="text-red-500 text-sm"
                            >{{ form.errors.media }}</span
                        >
                    </div>

                    <!-- Tags Section -->
                    <div class="mb-4">
                        <label
                            for="tags"
                            class="block text-sm font-medium text-gray-700"
                            >Tags</label
                        >
                        <div class="flex items-center mb-2">
                            <input
                                v-model="tagInput"
                                type="text"
                                placeholder="Add a tag"
                                @input="filterTags(tagInput)"
                                @keypress.preventEnter
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            />
                            <button
                                type="button"
                                @click="addTag(tagInput)"
                                class="ml-2 bg-blue-500 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-all"
                                style="height: 42px; line-height: 1.25"
                            >
                                Add
                            </button>
                        </div>

                        <!-- Tags Suggestions Dropdown -->
                        <ul
                            v-if="filteredTags.length"
                            class="border border-gray-300 rounded-md bg-white absolute mt-1 w-full z-10"
                        >
                            <li
                                v-for="(tag, index) in filteredTags"
                                :key="index"
                                @click="addTag(tag.name)"
                                class="cursor-pointer p-2 hover:bg-blue-100"
                            >
                                {{ tag.name }}
                            </li>
                        </ul>

                        <!-- Display selected tags -->
                        <div class="flex flex-wrap mt-2">
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

                        <!-- Error message for tags if any -->
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
