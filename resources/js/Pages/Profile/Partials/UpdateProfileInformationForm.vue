<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    gender: props.user.gender || '',
    date_of_birth: props.user.date_of_birth || '',
    mailing_address: props.user.mailing_address || '',
    country: props.user.country || '',
    photo: null,
});

const countries = ref(["Malaysia", "Singapore"]);

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Profile Information
        </template>

        <template #description>
            Update your account's profile information.
        </template>

        <template #form>
            <!-- Existing fields here (Photo, Name, Email) -->

            <!-- Gender -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="gender" value="Gender" />
                <select id="gender" v-model="form.gender" class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="" disabled>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                    <option value="Prefer not to say">Prefer not to say</option>
                </select>
                <InputError :message="form.errors.gender" class="mt-2" />
            </div>

            <!-- Date of Birth -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="date_of_birth" value="Date of Birth" />
                <TextInput
                    id="date_of_birth"
                    v-model="form.date_of_birth"
                    type="date"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.date_of_birth" class="mt-2" />
            </div>

            <!-- Mailing Address -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="mailing_address" value="Mailing Address" />
                <TextInput
                    id="mailing_address"
                    v-model="form.mailing_address"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.mailing_address" class="mt-2" />
            </div>

            <!-- Country Dropdown -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="country" value="Country" />
                <select id="country" v-model="form.country" class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="" disabled>Select Country</option>
                    <option v-for="country in countries" :key="country" :value="country">{{ country }}</option>
                </select>
                <InputError :message="form.errors.country" class="mt-2" />
            </div>
        </template>

        <!-- Actions -->
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
