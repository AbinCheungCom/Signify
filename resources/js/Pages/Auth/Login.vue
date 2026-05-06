<template>
  <div class="font-sans antialiased min-h-screen bg-white flex items-center justify-center">
    <!-- Left Panel - Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-[#171A20] items-center justify-center p-16">
      <div class="text-center">
        <h1 class="text-3xl font-medium text-white tracking-tight mb-4">Signify</h1>
        <p class="text-gray-400">企业家形象资产数字化系统</p>
      </div>
    </div>

    <!-- Right Panel - Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
      <div class="w-full max-w-md">
        <div class="mb-8">
          <h2 class="text-2xl font-medium text-gray-900 tracking-tight">登录</h2>
          <p class="text-gray-400 text-sm mt-2">访问您的企业家档案</p>
        </div>

        <!-- Session Status -->
        <div v-if="$page.props.flash.success" class="mb-4 p-3 bg-green-50 text-green-700 text-sm rounded">
          {{ $page.props.flash.success }}
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

          <div>
            <label class="block text-sm text-gray-600 mb-2">密码</label>
            <input
              v-model="form.password"
              type="password"
              required
              class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
              placeholder="••••••••"
            />
            <div v-if="errors.password" class="mt-1 text-xs text-red-500">{{ errors.password }}</div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input type="checkbox" v-model="form.remember" class="rounded border-gray-300" />
              <span class="ml-2 text-sm text-gray-500">记住我</span>
            </label>
            <Link href="/forgot-password" class="text-sm text-[#3E6AE1] hover:underline">忘记密码？</Link>
          </div>

          <button
            type="submit"
            :disabled="processing"
            class="w-full px-6 py-3 bg-[#3E6AE1] text-white font-medium rounded hover:bg-[#3558c4] transition-colors duration-300 disabled:opacity-50"
          >
            {{ processing ? '登录中...' : '登录' }}
          </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-400">
          还没有账号？ <Link href="/register" class="text-[#3E6AE1] hover:underline">立即注册</Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'

defineProps({
  canResetPassword: Boolean,
  status: String,
  errors: Object
})

const form = useForm({
  email: '',
  password: '',
  remember: false
})

const submit = () => {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  })
}
</script>