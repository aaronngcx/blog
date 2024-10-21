<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import AppLayout from "@/Layouts/AppLayout.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    canEdit: {
        type: Boolean,
        default: false,
    },
    auth: {
        type: Object,
        default: () => null,
    },
});

const author = ref(props.auth ? props.auth.name : "");
const content = ref("");
const editingCommentId = ref(null);
const updatedContent = ref("");

if (props.auth.user) {
    author.value = props.auth.user.name;
}
const handleCommentSubmit = () => {
    router.post(route("comments.store"), {
        content: content.value,
        post_id: props.post.id,
    });

    content.value = "";
};

const isImage = (media) => {
    return media && media.file_type && media.file_type.startsWith("image/");
};

const isPDF = (media) => {
    return (
        media &&
        media.file_type &&
        media.file_type.startsWith("application/pdf")
    );
};

function isAuthor(user_id) {
    return props.auth.user && props.auth.user.id === user_id;
}

function editComment(comment) {
    editingCommentId.value = comment.id;
    updatedContent.value = comment.content;
}

function cancelEdit() {
    editingCommentId.value = null;
    updatedContent.value = "";
}

function updateComment(commentId) {
    Inertia.post(
        route("comments.update", commentId),
        {
            content: updatedContent.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                editingCommentId.value = null;
                updatedContent.value = "";
            },
            onError: (errors) => {
                console.log(errors);
            },
        }
    );
}

function deleteComment(commentId) {
    if (confirm("Are you sure you want to delete this comment?")) {
        Inertia.delete(route("comments.destroy", commentId));
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

                <div
                    v-html="post.content"
                    class="prose prose-lg max-w-none"
                ></div>

                <!-- Media Uploads Section -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold">Media Uploads</h3>
                    <div
                        class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                    >
                        <div v-if="post.media_uploads.length > 0">
                            <div
                                v-for="media in post.media_uploads"
                                :key="media.id"
                                class="border rounded-lg overflow-hidden mb-4"
                            >
                                <template v-if="isImage(media)">
                                    <img
                                        :src="media.file_path"
                                        :alt="media.name"
                                        class="w-full h-64 object-cover"
                                    />
                                </template>
                                <template v-else-if="isPDF(media)">
                                    <div class="p-4 text-gray-700">
                                        <p>{{ media.name }}</p>
                                        <a
                                            :href="media.file_path"
                                            target="_blank"
                                            class="text-blue-600 underline"
                                            >Download PDF</a
                                        >
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="p-4 text-gray-700">
                                        <p>
                                            No preview available for this file
                                            type
                                        </p>
                                        <a
                                            :href="media.file_path"
                                            target="_blank"
                                            class="text-blue-600 underline"
                                            >Download {{ media.name }}</a
                                        >
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div v-else class="text-gray-600">No media uploads</div>
                    </div>
                </div>

                <SectionBorder />

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
                        <span
                            v-if="post.tags.length === 0"
                            class="text-gray-500"
                            >No Tags</span
                        >
                    </div>
                </div>

                <SectionBorder />

                <!-- Edit and Delete Buttons -->
                <div class="mt-4 flex justify-between items-center">
                    <p class="text-sm text-gray-500">
                        Posted on
                        {{ new Date(post.created_at).toLocaleDateString() }}
                    </p>
                    <div v-if="canEdit" class="flex space-x-4">
                        <Link
                            :href="route('posts.edit', post.id)"
                            class="btn btn-primary bg-blue-600 text-white px-4 py-2 rounded"
                        >
                            Edit
                        </Link>
                        <form @submit.prevent="handleDelete()">
                            <button
                                type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md transition duration-200"
                            >
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
                    <div
                        v-if="post.comments.length === 0"
                        class="text-gray-500"
                    >
                        No comments yet.
                    </div>
                    <div v-else>
                        <ul class="mt-4">
                            <li
                                v-for="comment in post.comments"
                                :key="comment.id"
                                class="bg-white p-4 rounded-lg mb-4"
                            >
                                <strong>{{
                                    comment.user?.name || "Anonymous"
                                }}</strong>
                                <p class="mt-1">{{ comment.content }}</p>
                                <p class="text-gray-500 text-xs mt-1">
                                    {{
                                        new Date(
                                            comment.created_at
                                        ).toLocaleDateString()
                                    }}
                                </p>

                                <!-- Edit Button -->
                                <button
                                    v-if="isAuthor(comment.user_id)"
                                    @click="editComment(comment)"
                                    class="text-sm text-blue-500"
                                >
                                    Edit
                                </button>

                                <!-- Delete Button -->
                                <button
                                    v-if="isAuthor(comment.user_id)"
                                    @click="deleteComment(comment.id)"
                                    class="text-sm text-red-500 ml-2"
                                >
                                    Delete
                                </button>

                                <!-- Edit Form (shown when editing) -->
                                <div v-if="editingCommentId === comment.id">
                                    <textarea
                                        v-model="updatedContent"
                                        class="mt-2 block w-full border-gray-300 rounded-md shadow-sm"
                                    ></textarea>
                                    <button
                                        @click="updateComment(comment.id)"
                                        class="mt-2 bg-green-600 text-white py-2 px-4 rounded-md"
                                    >
                                        Save
                                    </button>
                                    <button
                                        @click="cancelEdit"
                                        class="mt-2 text-gray-500"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <form @submit.prevent="handleCommentSubmit" class="mb-6">
                    <div>
                        <label
                            for="author"
                            class="block text-sm font-medium text-gray-700"
                            >Your Name</label
                        >
                        <input
                            :value="
                                props.auth.user
                                    ? props.auth.user.name
                                    : 'Anonymous'
                            "
                            type="text"
                            id="author"
                            readonly
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-200 cursor-not-allowed"
                        />
                    </div>
                    <div class="mt-4">
                        <label
                            for="content"
                            class="block text-sm font-medium text-gray-700"
                            >Comment</label
                        >
                        <textarea
                            v-model="content"
                            id="content"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        ></textarea>
                    </div>
                    <button
                        type="submit"
                        class="mt-4 bg-blue-600 text-white py-2 px-4 rounded-md"
                    >
                        Submit Comment
                    </button>
                </form>
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
