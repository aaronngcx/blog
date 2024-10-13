<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3'
import { ref } from 'vue';

const props = defineProps({
    posts: {
        type: Array,
        required: true
    }
});

const successMessage = ref('');

const { delete: destroy } = useForm({
        id: '',
})

function handleDelete(id) {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(route('posts.destroy', id), {
            onSuccess: () => {
                successMessage.value = 'Post deleted successfully!';
                Inertia.visit(route('posts.index'));
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
                <div class="flex justify-end mb-6">
                    <Link :href="route('posts.create')" class="btn btn-primary bg-blue-600 text-white p-2 rounded">Create New Post</Link>
                </div>

                <div v-if="successMessage" class="bg-green-100 text-green-800 p-4 rounded-lg mb-4">
                    {{ successMessage }}
                </div>

                <div v-if="posts.length">
                    <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                        <div v-for="post in posts" :key="post.id" class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h3 class="text-lg font-semibold">
                                <Link :href="route('posts.show', post.id)"  class="text-blue-600 hover:underline">
                                    {{ post.title }}
                                </Link>
                            </h3>
                            <p class="mt-2 text-gray-600">{{ post.meta_description }}</p>
                            <p class="mt-4 text-sm text-gray-400">Created by </p>
                            <SectionBorder />
                            <div class="flex justify-between mt-4">
                                <Link :href="route('posts.edit', post.id)" class="text-indigo-600 hover:underline">Edit</Link>
                                <form :action="`/posts/${post.id}`" method="POST" @submit.prevent="handleDelete(post.id)">
                                    <input type="hidden" name="_method" value="DELETE" />
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else>
                    <p class="text-center text-gray-600">No posts available.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
