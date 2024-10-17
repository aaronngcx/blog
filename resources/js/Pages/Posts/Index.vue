<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    posts: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        default: () => null,
    }
});

const successMessage = ref('');
const searchQuery = ref('');

function handleSearch() {
    if (searchQuery.value) {
        router.get(route('posts.index'), { search: searchQuery.value, page: 1 }, { preserveState: true });
    } else {
        router.get(route('posts.index'), { page: 1 }, { preserveState: true });
    }
}

const { delete: destroy } = useForm({
    id: '',
});

function handleDelete(id) {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(route('posts.destroy', id), {
            onSuccess: () => {
                successMessage.value = 'Post deleted successfully!';
            },
            preserveScroll: true
        });
    }
}
</script>

<template>
    <AppLayout title="Posts">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                All Posts
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex justify-between mb-6">
                    <Link 
                        v-if="auth.user" 
                        :href="route('posts.create')" 
                        class="btn btn-primary bg-blue-600 text-white p-2 rounded">
                        Create New Post
                    </Link>

                    <input
                        type="text"
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                        placeholder="Search posts..."
                        class="border border-gray-300 rounded-md p-2"
                    />
                </div>

                <div v-if="successMessage" class="bg-green-100 text-green-800 p-4 rounded-lg mb-4">
                    {{ successMessage }}
                </div>

                <div v-if="props.posts.data.length">
                    <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                        <div v-for="post in props.posts.data" :key="post.id" class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h3 class="text-lg font-semibold">
                                <Link :href="route('posts.show', post.url_slug)" class="text-blue-600 hover:underline">
                                    {{ post.title }}
                                </Link>
                            </h3>
                            <p class="mt-2 text-gray-600">{{ post.meta_description }}</p>
                            <p class="mt-4 text-sm text-gray-400">Created by {{ post.user.name }}</p>
                            <div v-if="auth.user">
                                <SectionBorder />
                                <div class="flex justify-between mt-4">
                                    <Link :href="route('posts.edit', post.id)" class="text-indigo-600 hover:underline">Edit</Link>
                                    <button type="button" @click="handleDelete(post.id)" class="text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination Links -->
                    <div class="mt-6">
                        <nav aria-label="Pagination">
                            <ul class="flex justify-center space-x-4">
                                <li>
                                    <Link 
                                        v-if="props.posts.current_page > 1" 
                                        :href="route('posts.index', { search: searchQuery, page: props.posts.current_page - 1 })" 
                                        class="px-4 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">
                                        Previous
                                    </Link>
                                </li>
                                <li v-for="page in Array.from({ length: props.posts.last_page }, (_, i) => i + 1)" :key="page">
                                    <Link 
                                        :href="route('posts.index', { search: searchQuery, page })" 
                                        class="px-4 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300"
                                        :class="{ 'bg-blue-500 text-white': props.posts.current_page === page }">
                                        {{ page }}
                                    </Link>
                                </li>
                                <li>
                                    <Link 
                                        v-if="props.posts.current_page < props.posts.last_page" 
                                        :href="route('posts.index', { search: searchQuery, page: props.posts.current_page + 1 })" 
                                        class="px-4 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">
                                        Next
                                    </Link>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <div v-else>
                    <p class="text-center text-gray-600">No posts available.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

