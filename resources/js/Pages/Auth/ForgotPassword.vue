<template>
  <div class="font-sans antialiased min-h-screen bg-white flex items-center justify-center p-8">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-medium text-gray-900 tracking-tight">Signify</h1>
        <p class="text-gray-400 text-sm mt-2">重置密码</p>
      </div>

      <div v-if="status" class="mb-4 p-4 bg-green-50 text-green-700 text-sm rounded">
        {{ status }}
      </div>

      <form @submit.prevent="submit" class="space-y-5">
        <div>
          <label class="block text-sm text-gray-600 mb-2">邮箱</label>
          <input
            v-model="form.email"
            type="email"
            required
            autofocus
            class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
            placeholder="you@example.com"
          />
          <div v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email }}</div>
        </div>

        <button
          type="submit"
          :disabled="processing"
          class="w-full px-6 py-3 bg-[#3E6AE1] text-white font-medium rounded hover:bg-[#3558c4] transition-colors duration-300 disabled:opacity-50"
        >
          {{ processing ? '发送中...' : '发送重置链接' }}
        </button>
      </form>

      <div class="mt-8 text-center">
        <Link href="/login" class="text-sm text-[#3E6AE1] hover:underline">返回登录</Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'

defineProps({
  errors: Object,
  status: String
})

const form = useForm({
  email: ''
})

const submit = () => {
  form.post('/forgot-password')
}
</script>