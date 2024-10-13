<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
    recentlySuccessful: Boolean,
});

</script>

<template>
    <AppLayout title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <!-- Update Profile Information -->
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" :recentlySuccessful="recentlySuccessful" />
                    <SectionBorder />
                </div>

                <!-- Update Password -->
                <div v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm :recentlySuccessful="recentlySuccessful" class="mt-10 sm:mt-0" />
                    <SectionBorder />
                </div>

                <!-- Two Factor Authentication -->
                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0"
                    />
                    <SectionBorder />
                </div>

                <!-- Logout Other Browser Sessions -->
                <div v-if="sessions && sessions.length">
                    <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />
                    <SectionBorder />
                </div>

                <!-- Delete User Account -->
                <div v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                    <DeleteUserForm class="mt-10 sm:mt-0" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
