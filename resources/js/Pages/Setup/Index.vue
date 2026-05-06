<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4">
    <div class="max-w-xl w-full">
      <!-- Logo -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-indigo-600">Signify</h1>
        <p class="text-gray-600 mt-2">企业家形象资产数字化系统 - 安装向导</p>
      </div>

      <!-- 状态提示 -->
      <div v-if="installed" class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
        <div class="flex items-center">
          <div class="text-green-500 text-2xl mr-4">✓</div>
          <div>
            <h3 class="font-semibold text-green-800">系统已安装</h3>
            <p class="text-green-700 text-sm mt-1">Signify 已完成配置，您可以开始使用。</p>
          </div>
        </div>
        <div class="mt-4 flex space-x-4">
          <Link href="/" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            访问首页
          </Link>
          <Link href="/login" class="px-4 py-2 border border-green-600 text-green-600 rounded-md hover:bg-green-50">
            登录后台
          </Link>
        </div>
      </div>

      <!-- 安装表单 -->
      <div v-else class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
          <h2 class="text-lg font-semibold text-gray-900">数据库配置</h2>
        </div>

        <form @submit.prevent="submit" class="p-6 space-y-4">
          <!-- 数据库配置 -->
          <div class="border-b pb-4 mb-4">
            <h3 class="text-sm font-medium text-gray-700 mb-3">数据库连接</h3>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">主机</label>
                <input
                  v-model="form.host"
                  type="text"
                  required
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="127.0.0.1"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">端口</label>
                <input
                  v-model="form.port"
                  type="number"
                  required
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="3306"
                />
              </div>
            </div>

            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">数据库名</label>
              <input
                v-model="form.database"
                type="text"
                required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="signify"
              />
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">用户名</label>
                <input
                  v-model="form.username"
                  type="text"
                  required
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="root"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">密码</label>
                <input
                  v-model="form.password"
                  type="password"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="可选"
                />
              </div>
            </div>

            <button
              type="button"
              @click="testDb"
              :disabled="testingDb"
              class="mt-3 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 disabled:opacity-50"
            >
              {{ testingDb ? '测试中...' : '测试连接' }}
            </button>

            <div v-if="dbTestResult !== null" class="mt-2 text-sm" :class="dbTestResult ? 'text-green-600' : 'text-red-600'">
              {{ dbTestMessage }}
            </div>
          </div>

          <!-- 系统配置 -->
          <div>
            <h3 class="text-sm font-medium text-gray-700 mb-3">系统信息</h3>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">网站名称</label>
              <input
                v-model="form.app_name"
                type="text"
                required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Signify"
              />
            </div>
          </div>

          <!-- 管理员账号 -->
          <div>
            <h3 class="text-sm font-medium text-gray-700 mb-3">管理员账号</h3>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">管理员邮箱</label>
              <input
                v-model="form.admin_email"
                type="email"
                required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="admin@example.com"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">管理员密码</label>
              <input
                v-model="form.admin_password"
                type="password"
                required
                minlength="6"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="至少6位"
              />
            </div>
          </div>

          <!-- 错误提示 -->
          <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-md">
            <p class="text-red-700 text-sm">{{ error }}</p>
          </div>

          <!-- 提交 -->
          <div class="pt-4">
            <button
              type="submit"
              :disabled="installing"
              class="w-full px-4 py-3 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ installing ? '安装中...' : '开始安装' }}
            </button>
          </div>
        </form>
      </div>

      <!-- 页脚 -->
      <div class="text-center mt-6 text-sm text-gray-500">
        Signify v1.0 - 企业家形象资产数字化系统
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

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
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
      body: JSON.stringify({
        host: form.host,
        port: form.port,
        database: form.database,
        username: form.username,
        password: form.password,
      })
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
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
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

const getCsrf = () => {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}
</script>