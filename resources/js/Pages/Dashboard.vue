<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Components/Welcome.vue";
import { Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    posts: {
        type: Array,
        required: true,
    },
    tags: {
        type: Array,
        required: true,
    },
});

// Reactive variable for selected tag
const selectedTag = ref(null);

// Computed property to filter posts by selected tag
const filteredPosts = computed(() => {
    if (!selectedTag.value) return props.posts;
    return props.posts.filter((post) =>
        post.tags.some((tag) => tag.id === selectedTag.value)
    );
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

                    <!-- Tag Filter -->
                    <div class="p-5">
                        <label
                            for="tags"
                            class="block text-sm font-medium text-gray-700"
                            >Filter by Tag:</label
                        >
                        <select
                            v-model="selectedTag"
                            id="tags"
                            class="mt-1 block w-full border-gray-300 rounded-md"
                        >
                            <option value="">All Tags</option>
                            <option
                                v-for="tag in props.tags"
                                :key="tag.id"
                                :value="tag.id"
                            >
                                {{ tag.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Display recent posts -->
                    <div class="p-5">
                        <h3 class="text-lg font-semibold mb-4 border-b pb-2">
                            Recent Posts
                        </h3>
                        <div v-if="filteredPosts.length">
                            <ul class="space-y-4">
                                <li
                                    v-for="post in filteredPosts"
                                    :key="post.url_slug"
                                    class="p-4 border rounded-lg bg-gray-50 hover:shadow-md transition-shadow"
                                >
                                    <h4 class="text-md font-bold">
                                        <Link
                                            :href="
                                                route(
                                                    'posts.show',
                                                    post.url_slug
                                                )
                                            "
                                            class="text-blue-600 hover:underline"
                                            >{{ post.title }}</Link
                                        >
                                    </h4>
                                    <p class="mt-2 text-gray-600">
                                        {{ post.meta_description }}
                                    </p>

                                    <!-- Display tags here -->
                                    <div class="mt-2">
                                        <span class="text-gray-500 text-sm"
                                            >Tags:
                                        </span>
                                        <span v-if="post.tags.length">
                                            <span
                                                v-for="(
                                                    tag, index
                                                ) in post.tags"
                                                :key="tag.id"
                                                class="inline-block bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded"
                                            >
                                                {{ tag.name }}
                                            </span>
                                        </span>
                                        <span v-else class="text-gray-500"
                                            >No tags</span
                                        >
                                    </div>

                                    <div
                                        class="mt-2 flex justify-between items-center"
                                    >
                                        <span class="text-gray-500 text-sm">
                                            Created on:
                                            {{
                                                new Date(
                                                    post.created_at
                                                ).toLocaleDateString()
                                            }}
                                        </span>
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
