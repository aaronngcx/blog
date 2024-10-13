<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import { router } from '@inertiajs/vue3'
import { ref } from 'vue';

const props = defineProps({
    post: {
        type: Object,
        required: true
    },
    canEdit: {
        type: Boolean,
        default: false
    }
});

const successMessage = ref('');

const { delete: destroy } = useForm({
        id: '',
})

function handleDelete() {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(route('posts.destroy', props.post.id), {
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
    <AppLayout :title="`Post - ${post.title}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ post.title }}
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-600 mb-4">{{ post.meta_description }}</p>
                
                <div v-html="post.content" class="prose prose-lg max-w-none"></div>

                <!-- Tags Section -->
                <div class="mt-4">
                    <h3 class="text-lg font-semibold">Tags</h3>
                    <div class="flex space-x-2 mt-2">
                        <span
                            v-for="tag in post.tags"
                            :key="tag.id"
                            class="inline-block bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm"
                        >
                            {{ tag.name }}
                        </span>
                    </div>
                </div>

                <SectionBorder />

                <!-- Edit and Delete Buttons -->
                <div class="mt-4 flex justify-between items-center">
                    <p class="text-sm text-gray-500">Posted on {{ new Date(post.created_at).toLocaleDateString() }}</p>
                    <div v-if="canEdit" class="flex space-x-4">
                        <Link :href="route('posts.edit', post.id)" class="btn btn-primary bg-blue-600 text-white px-4 py-2 rounded">
                            Edit
                        </Link>
                        <form @submit.prevent="handleDelete(post.id)">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md transition duration-200">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.prose h1 {
    font-size: 2.5rem;
    font-weight: bold;
}
.prose p {
    font-size: 1.125rem;
    color: #4a5568;
}
</style>
