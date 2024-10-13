<script setup>
import { ref } from "vue";
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
});

function submit() {
    form.put(route('posts.update', props.post.id), {
        preserveScroll: true,
        onSuccess: () => {
            alert('Post updated successfully!')
        }
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
                    <div>
                        <label
                            for="title"
                            class="block text-sm font-medium text-gray-700"
                        >Title</label>
                        <input
                            v-model="form.title"
                            type="text"
                            id="title"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            required
                        />
                        <span
                            v-if="form.errors.title"
                            class="text-red-500 text-sm"
                        >{{ form.errors.title }}</span>
                    </div>

                    <div>
                        <label
                            for="content"
                            class="block text-sm font-medium text-gray-700"
                        >Content</label>
                    </div>
                    <MyEditor v-model="form.content"/>

                    <div>
                        <label
                            for="url_slug"
                            class="block text-sm font-medium text-gray-700"
                        >URL Slug</label>
                        <input
                            v-model="form.url_slug"
                            type="text"
                            id="url_slug"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        />
                        <span
                            v-if="form.errors.url_slug"
                            class="text-red-500 text-sm"
                        >{{ form.errors.url_slug }}</span>
                    </div>

                    <div>
                        <label
                            for="meta_description"
                            class="block text-sm font-medium text-gray-700"
                        >Meta Description</label>
                        <input
                            v-model="form.meta_description"
                            type="text"
                            id="meta_description"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        />
                        <span
                            v-if="form.errors.meta_description"
                            class="text-red-500 text-sm"
                        >{{ form.errors.meta_description }}</span>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary bg-blue-600 text-white p-2 rounded">
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
