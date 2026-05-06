<template>
  <div class="font-sans antialiased min-h-screen bg-white flex items-center justify-center p-8">
    <div class="w-full max-w-md text-center">
      <div class="mb-6">
        <div class="text-4xl text-gray-300 mb-4">✉</div>
        <h1 class="text-2xl font-medium text-gray-900 tracking-tight">验证邮箱</h1>
        <p class="text-gray-400 text-sm mt-2">我们已发送验证链接至您的邮箱</p>
      </div>

      <div v-if="status === 'verification-link-sent'" class="mb-6 p-4 bg-green-50 text-green-700 text-sm rounded">
        新的验证链接已发送至您的邮箱
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <button
          type="submit"
          class="w-full px-6 py-3 bg-[#3E6AE1] text-white font-medium rounded hover:bg-[#3558c4] transition-colors duration-300"
        >
          重新发送验证邮件
        </button>
      </form>

      <div class="mt-6">
        <Link href="/login" class="text-sm text-[#3E6AE1] hover:underline">返回登录</Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'

defineProps({
  status: String
})

const form = useForm({})

const submit = () => {
  form.post('/email/verification-notification')
}
</script>