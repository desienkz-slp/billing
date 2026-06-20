<template>
    <AppLayout :title="pageTitle">
        <div class="p-6">
            <div class="max-w-full h-full flex flex-col min-h-0 w-full mx-auto">
                <div class="mb-6 flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Edit Pelanggan: {{ customer.name }}</h1>
                    <button @click="goBack" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">
                        Kembali
                    </button>
                </div>
                
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <CustomerForm 
                        :customer="customer"
                        :areas="areas"
                        :packages="packages"
                        :routers="routers"
                        :servers="servers"
                        :odps="odps"
                        :users="users"
                        @close="goBack"
                        @success="goBack"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CustomerForm from './Components/CustomerForm.vue';

const props = defineProps({
    customer: Object,
    areas: Array,
    packages: Array,
    routers: Array,
    servers: Array,
    odps: Array,
    users: Array,
});

const pageTitle = computed(() => {
    return 'Edit Pelanggan - ' + props.customer.name;
});

const goBack = () => {
    router.visit(route('cust.customers.index'));
};
</script>
