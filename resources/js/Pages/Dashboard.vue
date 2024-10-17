<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    posts: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Home
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome />

                    <!-- Display posts -->
                    <div class="mt-6 p-6">
                        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Recent Posts</h3>
                        <div v-if="posts.length">
                            <ul class="space-y-4">
                                <li v-for="post in posts" :key="post.url_slug" class="p-4 border rounded-lg bg-gray-50 hover:shadow-md transition-shadow">
                                    <h4 class="text-md font-bold">
                                        <Link :href="route('posts.show', post.url_slug)" class="text-blue-600 hover:underline">{{ post.title }}</Link>
                                    </h4>
                                    <p class="mt-2 text-gray-600">{{ post.meta_description }}</p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-gray-500 text-sm">Created on: {{ new Date(post.created_at).toLocaleDateString() }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div v-else>
                            <p class="text-gray-500">No posts available.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

