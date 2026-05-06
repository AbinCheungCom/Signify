<template>
  <div class="font-sans antialiased min-h-screen bg-white">
    <!-- Header -->
    <div class="pt-20 pb-12 text-center">
      <h1 class="text-4xl font-medium text-gray-900 tracking-tight">Signify</h1>
      <p class="text-gray-500 mt-3">企业家形象资产数字化系统 · 安装向导</p>
    </div>

    <!-- Installed State -->
    <div v-if="installed" class="max-w-md mx-auto px-8 mb-20">
      <div class="bg-[#F4F4F4] p-8 text-center">
        <div class="text-3xl mb-4">✓</div>
        <h2 class="text-xl font-medium text-gray-900 mb-2">系统已安装</h2>
        <p class="text-gray-500 text-sm">Signify 已完成配置，开始使用吧。</p>
        <div class="mt-8 flex justify-center space-x-4">
          <Link href="/" class="px-6 py-3 bg-[#3E6AE1] text-white text-sm font-medium rounded hover:bg-[#3558c4] transition-colors duration-300">
            访问首页
          </Link>
          <Link href="/login" class="px-6 py-3 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded hover:bg-gray-50 transition-colors duration-300">
            登录
          </Link>
        </div>
      </div>
    </div>

    <!-- Installation Form -->
    <div v-else class="max-w-md mx-auto px-8 mb-20">
      <div class="border border-[#EEEEEE]">
        <!-- Database Section -->
        <div class="p-8 border-b border-[#EEEEEE]">
          <h2 class="text-sm font-medium text-gray-900 mb-6">数据库连接</h2>

          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs text-gray-500 mb-2">主机</label>
                <input v-model="form.host" type="text" required class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20" placeholder="127.0.0.1" />
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-2">端口</label>
                <input v-model="form.port" type="number" required class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20" placeholder="3306" />
              </div>
            </div>

            <div>
              <label class="block text-xs text-gray-500 mb-2">数据库名</label>
              <input v-model="form.database" type="text" required class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20" placeholder="signify" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs text-gray-500 mb-2">用户名</label>
                <input v-model="form.username" type="text" required class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20" placeholder="root" />
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-2">密码</label>
                <input v-model="form.password" type="password" class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20" placeholder="可选" />
              </div>
            </div>

            <button type="button" @click="testDb" :disabled="testingDb" class="px-4 py-2 border border-[#EEEEEE] text-sm text-gray-500 rounded hover:bg-[#F4F4F4] transition-colors duration-300 disabled:opacity-50">
              {{ testingDb ? '测试中...' : '测试连接' }}
            </button>

            <div v-if="dbTestResult !== null" class="text-sm" :class="dbTestResult ? 'text-green-600' : 'text-red-600'">
              {{ dbTestMessage }}
            </div>
          </div>
        </div>

        <!-- System Section -->
        <div class="p-8 border-b border-[#EEEEEE]">
          <h2 class="text-sm font-medium text-gray-900 mb-6">系统信息</h2>
          <div>
            <label class="block text-xs text-gray-500 mb-2">网站名称</label>
            <input v-model="form.app_name" type="text" required class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20" placeholder="Signify" />
          </div>
        </div>

        <!-- Admin Section -->
        <div class="p-8">
          <h2 class="text-sm font-medium text-gray-900 mb-6">管理员账号</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-xs text-gray-500 mb-2">管理员邮箱</label>
              <input v-model="form.admin_email" type="email" required class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20" placeholder="admin@example.com" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-2">管理员密码</label>
              <input v-model="form.admin_password" type="password" required minlength="8" class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20" placeholder="至少8位" />
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-2">确认管理员密码</label>
              <input v-model="form.admin_password_confirmation" type="password" required minlength="8" class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20" placeholder="再次输入密码" />
            </div>
          </div>
        </div>

        <!-- Error -->
        <div v-if="error" class="px-8 pb-6">
          <div class="p-4 bg-red-50 border-l-4 border-red-500">
            <p class="text-red-700 text-sm">{{ error }}</p>
          </div>
        </div>

        <!-- Submit -->
        <div class="px-8 pb-8">
          <button type="submit" @click="submit" :disabled="installing" class="w-full px-6 py-4 bg-[#3E6AE1] text-white font-medium rounded hover:bg-[#3558c4] transition-colors duration-300 disabled:opacity-50">
            {{ installing ? '安装中...' : '开始安装' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="text-center pb-12 text-sm text-gray-400">
      Signify v1.0
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  dbConnected: Boolean,
  installed: Boolean,
  env: Object
})

const form = reactive({
  host: props.env?.DB_HOST || '127.0.0.1',
  port: props.env?.DB_PORT || 3306,
  database: props.env?.DB_DATABASE || '',
  username: props.env?.DB_USERNAME || 'root',
  password: '',
  app_name: props.env?.APP_NAME || 'Signify',
  admin_email: 'admin@signify.com',
  admin_password: '',
  admin_password_confirmation: '',
})

const testingDb = ref(false)
const dbTestResult = ref(null)
const dbTestMessage = ref('')
const installing = ref(false)
const error = ref('')

const testDb = async () => {
  testingDb.value = true
  dbTestResult.value = null
  try {
    const res = await fetch('/setup/test-db', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
      body: JSON.stringify({ host: form.host, port: form.port, database: form.database, username: form.username, password: form.password })
    })
    const data = await res.json()
    dbTestResult.value = data.success
    dbTestMessage.value = data.message
  } catch (e) {
    dbTestResult.value = false
    dbTestMessage.value = '连接失败'
  } finally {
    testingDb.value = false
  }
}

const submit = async () => {
  installing.value = true
  error.value = ''
  try {
    const res = await fetch('/setup/install', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
      body: JSON.stringify(form)
    })
    const data = await res.json()
    if (data.success) {
      router.visit('/')
    } else {
      error.value = data.message || '安装失败'
    }
  } catch (e) {
    error.value = '安装失败，请检查数据库配置'
  } finally {
    installing.value = false
  }
}
</script>