import { reactive } from 'vue'

const toasts = reactive([])
let nextId = 0

export function useToast() {
    function add(message, type = 'info', duration = 3500) {
        const id = ++nextId
        toasts.push({ id, message, type })
        setTimeout(() => remove(id), duration)
    }

    function remove(id) {
        const i = toasts.findIndex(t => t.id === id)
        if (i !== -1) toasts.splice(i, 1)
    }

    return {
        toasts,
        success: (msg) => add(msg, 'success'),
        error:   (msg) => add(msg, 'error'),
        info:    (msg) => add(msg, 'info'),
        warn:    (msg) => add(msg, 'warning'),
        remove,
    }
}
