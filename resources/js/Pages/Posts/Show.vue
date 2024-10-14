<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    post: {
        type: Object,
        required: true
    },
    comments: {
        type: Array,
        default: () => []
    },
    canEdit: {
        type: Boolean,
        default: false
    },
    auth: {
        type: Object,
        default: () => null
    }
});

const author = ref(props.auth ? props.auth.name : '');
const content = ref('');
if (props.auth.user) {
    author.value = props.auth.user.name;
}
const handleCommentSubmit = () => {
    router.post(route('comments.store', props.post.id), {
        author: author.value,
        content: content.value,
    });
    
    author.value = props.auth ? props.auth.name : '';
    content.value = '';
};
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
                        <form @submit.prevent="handleDelete()">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md transition duration-200">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold">Comments</h3>

                <div class="mt-4">
                    <div v-if="comments.length === 0" class="text-gray-500">No comments yet.</div>
                    <div v-else>
                        <!-- <h4 class="font-medium">All Comments:</h4> -->
                        <ul class="mt-4">
                            <li v-for="comment in comments" :key="comment.id" class="bg-white p-4 rounded-lg mb-4">
                                <strong>{{ comment.author }}</strong>
                                <p class="mt-1">{{ comment.content }}</p>
                                <p class="text-gray-500 text-xs mt-1">{{ new Date(comment.created_at).toLocaleDateString() }}</p>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Comment Form -->
                <div class="mt-6">
                    <form @submit.prevent="handleCommentSubmit" class="mb-6">
                        <div>
                            <label for="author" class="block text-sm font-medium text-gray-700">Your Name</label>
                            <input 
                                v-model="author" 
                                type="text" 
                                id="author" 
                                required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                            />
                        </div>
                        <div class="mt-4">
                            <label for="content" class="block text-sm font-medium text-gray-700">Comment</label>
                            <textarea v-model="content" id="content" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                        <button type="submit" class="mt-4 bg-blue-600 text-white py-2 px-4 rounded-md">Submit Comment</button>
                    </form>
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
