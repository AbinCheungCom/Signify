<template>
  <div class="min-h-screen bg-gray-50">
    <!-- 导航栏 -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <Link href="/" class="text-xl font-bold text-gray-900">Signify</Link>
          </div>
          <div class="flex items-center space-x-4">
            <Link href="/" class="text-gray-600 hover:text-gray-900">首页</Link>
            <Link href="/entrepreneurs" class="text-gray-600 hover:text-gray-900">企业家库</Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- 主内容 -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
          <h1 class="text-xl font-bold text-gray-900">个人中心</h1>
        </div>

        <!-- 消息提示 -->
        <div v-if="$page.props.flash.success" class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
          <p class="text-green-700">{{ $page.props.flash.success }}</p>
        </div>
        <div v-if="$page.props.flash.error" class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
          <p class="text-red-700">{{ $page.props.flash.error }}</p>
        </div>

        <!-- 未创建档案 -->
        <div v-if="!entrepreneur" class="p-6">
          <p class="text-gray-600 mb-4">您尚未创建企业家档案</p>
          <form @submit.prevent="createProfile">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">姓名 *</label>
              <input
                v-model="createForm.name"
                type="text"
                required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="请输入您的姓名"
              />
            </div>
            <button
              type="submit"
              class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
            >
              创建档案
            </button>
          </form>
        </div>

        <!-- 已创建档案 -->
        <div v-else class="p-6">
          <!-- 状态提示 -->
          <div
            v-if="entrepreneur.status === 'pending'"
            class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md"
          >
            <p class="text-yellow-700">您的档案正在审核中，请耐心等待...</p>
          </div>
          <div
            v-else-if="entrepreneur.status === 'rejected'"
            class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md"
          >
            <p class="text-red-700">您的档案审核未通过，请修改后重新提交</p>
          </div>

          <form @submit.prevent="submit">
            <!-- 头像 -->
            <div class="mb-6 flex items-center">
              <div class="w-24 h-24 bg-gray-200 rounded-full overflow-hidden mr-6">
                <img
                  v-if="form.avatar_preview || entrepreneur.avatar"
                  :src="form.avatar_preview || `/storage/${entrepreneur.avatar}`"
                  alt="头像"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-2xl font-bold">
                  {{ entrepreneur.name.charAt(0) }}
                </div>
              </div>
              <div>
                <input
                  type="file"
                  @change="previewAvatar"
                  accept="image/*"
                  class="hidden"
                  ref="avatarInput"
                />
                <button
                  type="button"
                  @click="$refs.avatarInput.click()"
                  class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                >
                  更换头像
                </button>
                <p class="text-xs text-gray-500 mt-1">支持 JPG、PNG，最大 2MB</p>
              </div>
            </div>

            <!-- 基本信息 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">姓名</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">行业</label>
                <input
                  v-model="form.industry"
                  type="text"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="如：科技、金融、制造"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">城市</label>
                <input
                  v-model="form.city"
                  type="text"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="如：北京、上海、深圳"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">联系电话</label>
                <input
                  v-model="form.contact_phone"
                  type="tel"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">联系邮箱</label>
                <input
                  v-model="form.contact_email"
                  type="email"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
            </div>

            <!-- 个人简介 -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">个人简介</label>
              <textarea
                v-model="form.bio"
                rows="4"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="简单介绍一下您的背景和成就..."
              ></textarea>
            </div>

            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="sending"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
              >
                {{ sending ? '保存中...' : '保存修改' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  entrepreneur: Object
})

const sending = ref(false)

const form = useForm({
  name: props.entrepreneur?.name || '',
  avatar: null,
  avatar_preview: null,
  industry: props.entrepreneur?.industry || '',
  city: props.entrepreneur?.city || '',
  bio: props.entrepreneur?.bio || '',
  contact_phone: props.entrepreneur?.contact_phone || '',
  contact_email: props.entrepreneur?.contact_email || '',
})

const createForm = useForm({
  name: ''
})

const previewAvatar = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.avatar = file
    form.avatar_preview = URL.createObjectURL(file)
  }
}

const submit = () => {
  sending.value = true
  form.post('/my/profile', {
    forceFormData: true,
    onFinish: () => {
      sending.value = false
    }
  })
}

const createProfile = () => {
  createForm.post('/my/profile')
}
</script>
