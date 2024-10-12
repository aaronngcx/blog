<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import { usePage } from '@inertiajs/vue3';

// Get the current user from Inertia's page props
// const { user } = usePage().props;
console.log
// Set up form handling with new fields: slug and user_id
const form = useForm({
    title: '',
    content: '',
    meta_description: '',
    slug: '',
});

// Function to handle form submission
const submit = () => {
    form.post('/posts', {
        onFinish: () => form.reset(),
    });
};
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
                    <!-- Title Field -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input
                            v-model="form.title"
                            type="text"
                            id="title"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            required
                        />
                        <span v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</span>
                    </div>

                    <!-- Content Field -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                        <textarea
                            v-model="form.content"
                            id="content"
                            rows="5"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            required
                        ></textarea>
                        <span v-if="form.errors.content" class="text-red-500 text-sm">{{ form.errors.content }}</span>
                    </div>

                    <!-- Meta Description Field -->
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                        <input
                            v-model="form.meta_description"
                            type="text"
                            id="meta_description"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        />
                        <span v-if="form.errors.meta_description" class="text-red-500 text-sm">{{ form.errors.meta_description }}</span>
                    </div>

                    <!-- Slug Field -->
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700">URL Slug</label>
                        <input
                            v-model="form.slug"
                            type="text"
                            id="slug"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                            required
                        />
                        <span v-if="form.errors.slug" class="text-red-500 text-sm">{{ form.errors.slug }}</span>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">Create Post</button>
                    </div>
                </div>
            </form>

            <SectionBorder />
        </div>
    </AppLayout>
</template>

<style scoped>
/* Add any scoped styles here if needed */
</style>
