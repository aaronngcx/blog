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

</script>

<template>
    <AppLayout :title="`Preview Post - ${post.title}`">
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
                        <div v-if="post.mediaUploads && post.mediaUploads.length > 0">
                            <div
                                v-for="media in post.mediaUploads"
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
                            v-if="!post.tags || post.tags.length === 0"
                            class="text-gray-500"
                            >No Tags</span
                        >
                    </div>
                </div>

                <SectionBorder />
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
