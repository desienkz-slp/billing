<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PHBhdGggZD0iTTIgMkgwdjJWMkgyem0xMCAwSDEwVjJ2Mmgyem0tNiA2SDhWMGgydjh6bTEyIDBIMjBWMGgydjh6TTEyIDZoMnYySDEyVjZ6bS02IDBIOFY2SDZ6bTEyIDBIMjBWNmgydjJ6IiBmaWxsPSJyZ2JhKDE1LDIzLDQyLDAuMDMpIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiLz48L3N2Zz4=')]">
        
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center text-indigo-600 dark:text-indigo-400">
                <svg viewBox="0 0 24 24" class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-slate-900 dark:text-white">
                LadaPala-Bill
            </h2>
            <p class="mt-2 text-center text-sm text-slate-600 dark:text-slate-400">
                Sistem Billing ISP Professional
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-slate-800 py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 border border-slate-100 dark:border-slate-700">
                
                <div v-if="$page.props.flash?.error" class="rounded-md bg-red-50 dark:bg-red-900/30 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                {{ $page.props.flash.error }}
                            </h3>
                        </div>
                    </div>
                </div>

                <form class="space-y-6" @submit.prevent="submit">
                    <Input 
                        id="username" 
                        label="Username" 
                        v-model="form.username" 
                        :error="form.errors.username"
                        required 
                        autofocus 
                    />

                    <div>
                        <label for="password" class="block type-body-sm font-medium text-charcoal dark:text-slate-300 mb-1">
                            Password
                        </label>
                        <div class="relative">
                            <input 
                                :type="showPassword ? 'text' : 'password'" 
                                id="password" 
                                v-model="form.password"
                                class="block w-full h-[44px] px-3 rounded-lg border border-hairline bg-canvas text-ink type-body-md focus:outline-none focus:ring-2 focus:ring-fb-blue focus:border-transparent shadow-none dark:bg-slate-800 dark:border-slate-600 dark:text-white"
                                required
                            >
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-500">
                                <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1 type-body-sm text-critical-strong">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" v-model="form.remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-slate-900 dark:text-slate-300">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <div>
                        <Button type="submit" class="w-full" :disabled="form.processing">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Masuk...' : 'Masuk ke Dashboard' }}
                        </Button>
                    </div>
                </form>
            </div>
            <p class="mt-8 text-center text-xs text-slate-500">
                &copy; 2026 upluk-upluk_dev version 2.0
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Input from '../../Components/Input.vue';
import Button from '../../Components/Button.vue';

const showPassword = ref(false);

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login.post'), {
        onFinish: () => form.reset('password'),
    });
};
</script>
