import { defineStore } from 'pinia'

export const useToastStore = defineStore('toast', {
    state: () => ({
        visible: false,
        type: 'success',
        message: '',
        timer: null,
    }),

    actions: {
        show(message, type = 'success') {
            if (this.timer) clearTimeout(this.timer)

            this.message = message
            this.type = type
            this.visible = true

            this.timer = setTimeout(() => {
                this.visible = false
            }, type === 'error' ? 4000 : 3500)
        },

        hide() {
            this.visible = false
            if (this.timer) clearTimeout(this.timer)
        },
    },
})
