<?php

$dir = __DIR__ . '/resources/js/Pages';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$boilerplate = <<<VUE
<template>
    <AppLayout :title="pageTitle">
        <div class="p-6 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 min-h-[500px] flex items-center justify-center">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                <h3 class="text-xl font-medium text-slate-500 dark:text-slate-400">Konten Kosong</h3>
                <p class="text-sm text-slate-400 dark:text-slate-500 mt-2">Halaman ini siap didesain ulang.</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const pageTitle = computed(() => {
    return 'LadaPala-Bill';
});
</script>
VUE;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'vue') {
        $path = $file->getPathname();
        
        // Skip Auth directory
        if (strpos($path, DIRECTORY_SEPARATOR . 'Auth' . DIRECTORY_SEPARATOR) !== false) {
            continue;
        }

        file_put_contents($path, $boilerplate);
        echo "Wiped: " . $path . "\n";
    }
}
echo "Done.\n";
