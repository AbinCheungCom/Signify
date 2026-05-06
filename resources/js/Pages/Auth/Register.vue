<template>
  <div class="font-sans antialiased min-h-screen bg-white flex items-center justify-center">
    <!-- Left Panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-[#171A20] items-center justify-center p-16">
      <div class="text-center">
        <h1 class="text-3xl font-medium text-white tracking-tight mb-4">Signify</h1>
        <p class="text-gray-400">企业家形象资产数字化系统</p>
      </div>
    </div>

    <!-- Right Panel -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
      <div class="w-full max-w-md">
        <div class="mb-8">
          <h2 class="text-2xl font-medium text-gray-900 tracking-tight">创建账号</h2>
          <p class="text-gray-400 text-sm mt-2">加入 Signify 管理您的企业家形象</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
          <div>
            <label class="block text-sm text-gray-600 mb-2">姓名</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
              placeholder="您的姓名"
            />
            <div v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name }}</div>
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-2">邮箱</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
              placeholder="you@example.com"
            />
            <div v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email }}</div>
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-2">密码</label>
            <input
              v-model="form.password"
              type="password"
              required
              class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
              placeholder="至少8位"
            />
            <div v-if="errors.password" class="mt-1 text-xs text-red-500">{{ errors.password }}</div>
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-2">确认密码</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              required
              class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
              placeholder="再次输入密码"
            />
          </div>

          <button
            type="submit"
            :disabled="processing"
            class="w-full px-6 py-3 bg-[#3E6AE1] text-white font-medium rounded hover:bg-[#3558c4] transition-colors duration-300 disabled:opacity-50"
          >
            {{ processing ? '注册中...' : '注册' }}
          </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-400">
          已有账号？ <Link href="/login" class="text-[#3E6AE1] hover:underline">立即登录</Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'

defineProps({
  errors: Object
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post('/register', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>