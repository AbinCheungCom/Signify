<template>
  <div class="font-sans antialiased min-h-screen bg-white flex items-center justify-center p-8">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-medium text-gray-900 tracking-tight">确认密码</h1>
        <p class="text-gray-400 text-sm mt-2">请输入您的密码以继续</p>
      </div>

      <form @submit.prevent="submit" class="space-y-5">
        <div>
          <label class="block text-sm text-gray-600 mb-2">密码</label>
          <input
            v-model="form.password"
            type="password"
            required
            autofocus
            class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
            placeholder="••••••••"
          />
          <div v-if="errors.password" class="mt-1 text-xs text-red-500">{{ errors.password }}</div>
        </div>

        <button
          type="submit"
          :disabled="form.processing || ! form.password"
          class="w-full px-6 py-3 bg-[#3E6AE1] text-white font-medium rounded hover:bg-[#3558c4] transition-colors duration-300 disabled:opacity-50"
        >
          确认
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

defineProps({
  errors: Object
})

const form = useForm({
  password: '',
})

const submit = () => {
  form.post('/confirm-password', {
    onFinish: () => form.reset(),
  })
}
</script>