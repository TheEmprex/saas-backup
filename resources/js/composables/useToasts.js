import { ref, reactive } from 'vue'

const bus = reactive({ toasts: [] })
let idCounter = 1

export function useToasts() {
  const toasts = bus.toasts

  const add = (message, type = 'info', duration = 2500) => {
    const id = idCounter++
    toasts.push({ id, message, type })
    if (duration > 0) {
      setTimeout(() => remove(id), duration)
    }
    return id
  }

  const remove = (id) => {
    const idx = toasts.findIndex(t => t.id === id)
    if (idx !== -1) toasts.splice(idx, 1)
  }

  const success = (msg, d = 2500) => add(msg, 'success', d)
  const error = (msg, d = 3000) => add(msg, 'error', d)
  const info = (msg, d = 2500) => add(msg, 'info', d)

  return { toasts, add, remove, success, error, info }
}

