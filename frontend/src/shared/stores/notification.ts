import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useNotificationStore = defineStore('notification', () => {
    const show = ref(false);
    const message = ref('');
    const color = ref('success'); // success, error, info, warning

    function notify(msg: string, type: 'success' | 'error' | 'info' | 'warning' = 'success') {
        message.value = msg;
        color.value = type;
        show.value = true;
    }

    function error(msg: string) {
        notify(msg, 'error');
    }

    function success(msg: string) {
        notify(msg, 'success');
    }

    return {
        show,
        message,
        color,
        notify,
        error,
        success
    };
});
