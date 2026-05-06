<template>
  <div class="font-sans antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/75 backdrop-blur-md">
      <div class="max-w-7xl mx-auto px-8 h-16 flex items-center justify-between">
        <Link href="/" class="text-xl font-medium tracking-tight text-gray-900">Signify</Link>
        <div class="flex items-center space-x-8">
          <Link href="/" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-300">首页</Link>
          <Link href="/entrepreneurs" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-300">企业家库</Link>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-24 pb-16 min-h-screen">
      <div class="max-w-2xl mx-auto px-8">
        <!-- Header -->
        <div class="mb-12">
          <h1 class="text-4xl font-medium text-gray-900 tracking-tight">个人中心</h1>
          <p class="text-gray-500 mt-2">管理您的企业家形象档案</p>
        </div>

        <!-- Success/Error Messages -->
        <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-[#F4F4F4] border-l-4 border-green-500">
          <p class="text-green-700 text-sm">{{ $page.props.flash.success }}</p>
        </div>
        <div v-if="$page.props.flash.error" class="mb-6 p-4 bg-[#F4F4F4] border-l-4 border-red-500">
          <p class="text-red-700 text-sm">{{ $page.props.flash.error }}</p>
        </div>

        <!-- No Profile -->
        <div v-if="!entrepreneur" class="bg-white p-8">
          <p class="text-gray-500 mb-6">您尚未创建企业家档案</p>
          <form @submit.prevent="createProfile" class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">姓名 *</label>
              <input
                v-model="createForm.name"
                type="text"
                required
                class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
                placeholder="请输入您的姓名"
              />
            </div>
            <button
              type="submit"
              class="w-full px-6 py-3 bg-[#3E6AE1] text-white font-medium rounded hover:bg-[#3558c4] transition-colors duration-300"
            >
              创建档案
            </button>
          </form>
        </div>

        <!-- Edit Profile -->
        <div v-else class="bg-white p-8">
          <!-- Status Badge -->
          <div v-if="entrepreneur.status === 'pending'" class="mb-6 p-4 bg-[#F4F4F4]">
            <p class="text-sm text-gray-500">您的档案正在审核中...</p>
          </div>

          <!-- Avatar -->
          <div class="flex items-center mb-10">
            <div class="w-24 h-24 bg-[#F4F4F4] rounded-full overflow-hidden mr-6">
              <img
                v-if="form.avatar_preview || entrepreneur.avatar"
                :src="form.avatar_preview || `/storage/${entrepreneur.avatar}`"
                alt="头像"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-300 text-3xl font-light">
                {{ entrepreneur.name.charAt(0) }}
              </div>
            </div>
            <div>
              <input type="file" @change="previewAvatar" accept="image/*" class="hidden" ref="avatarInput" />
              <button
                type="button"
                @click="$refs.avatarInput.click()"
                class="px-4 py-2 border border-gray-200 text-sm text-gray-600 rounded hover:bg-[#F4F4F4] transition-colors duration-300"
              >
                更换头像
              </button>
              <p class="text-xs text-gray-400 mt-2">支持 JPG、PNG，最大 2MB</p>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">姓名</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">行业</label>
                <input
                  v-model="form.industry"
                  type="text"
                  class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
                  placeholder="科技、金融、制造..."
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">城市</label>
                <input
                  v-model="form.city"
                  type="text"
                  class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
                  placeholder="北京、上海、深圳..."
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">联系电话</label>
                <input
                  v-model="form.contact_phone"
                  type="tel"
                  class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">联系邮箱</label>
              <input
                v-model="form.contact_email"
                type="email"
                class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">个人简介</label>
              <textarea
                v-model="form.bio"
                rows="4"
                class="w-full px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20 resize-none"
                placeholder="简单介绍一下您的背景和成就..."
              ></textarea>
            </div>

            <button
              type="submit"
              :disabled="sending"
              class="w-full px-6 py-3 bg-[#3E6AE1] text-white font-medium rounded hover:bg-[#3558c4] transition-colors duration-300 disabled:opacity-50"
            >
              {{ sending ? '保存中...' : '保存修改' }}
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="py-12 bg-white border-t border-[#EEEEEE]">
      <div class="max-w-7xl mx-auto px-8 text-center">
        <p class="text-sm text-gray-400">Signify · 企业家形象资产数字化系统</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
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