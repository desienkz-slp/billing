import { reactive } from 'vue';

const state = reactive({
    isOpen: false,
    title: 'Konfirmasi',
    message: 'Apakah Anda yakin?',
    confirmText: 'Ya',
    cancelText: 'Batal',
    confirmColor: 'sky',
    resolve: null,
    reject: null,
});

export function useConfirm() {
    const confirm = (options = {}) => {
        return new Promise((resolve, reject) => {
            state.title = options.title || 'Konfirmasi';
            state.message = options.message || 'Apakah Anda yakin?';
            state.confirmText = options.confirmText || 'Ya';
            state.cancelText = options.cancelText || 'Batal';
            state.confirmColor = options.confirmColor || 'sky';
            state.isOpen = true;
            state.resolve = resolve;
            state.reject = reject;
        });
    };

    const proceed = () => {
        if (state.resolve) state.resolve(true);
        state.isOpen = false;
    };

    const cancel = () => {
        if (state.resolve) state.resolve(false);
        state.isOpen = false;
    };

    return {
        state,
        confirm,
        proceed,
        cancel
    };
}
