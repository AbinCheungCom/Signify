<template>
  <div class="font-sans antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/75 backdrop-blur-md">
      <div class="max-w-7xl mx-auto px-8 h-16 flex items-center justify-between">
        <Link href="/" class="text-xl font-medium tracking-tight text-gray-900">Signify</Link>
        <div class="flex items-center space-x-8">
          <Link href="/entrepreneurs" class="text-sm font-medium text-gray-600 hover:text-gray-900">企业家库</Link>
          <template v-if="$page.props.auth.user">
            <Link href="/my/profile" class="text-sm font-medium text-gray-600 hover:text-gray-900">个人中心</Link>
          </template>
          <template v-else>
            <Link href="/login" class="text-sm font-medium text-gray-600 hover:text-gray-900">登录</Link>
          </template>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-24 pb-16 bg-[#F4F4F4] min-h-screen">
      <div class="max-w-4xl mx-auto px-8">
        <!-- Profile Card -->
        <div class="bg-white">
          <!-- Avatar Section -->
          <div class="p-12 text-center border-b border-[#EEEEEE]">
            <div class="w-40 h-40 mx-auto bg-[#F4F4F4] rounded-full overflow-hidden mb-6">
              <img
                v-if="entrepreneur.avatar"
                :src="`/storage/${entrepreneur.avatar}`"
                :alt="entrepreneur.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-300 text-5xl font-light">
                {{ entrepreneur.name.charAt(0) }}
              </div>
            </div>
            <h1 class="text-3xl font-medium text-gray-900 tracking-tight">{{ entrepreneur.name }}</h1>
            <p class="text-gray-500 mt-2">{{ entrepreneur.industry }} · {{ entrepreneur.city }}</p>
            <span v-if="entrepreneur.is_featured" class="inline-block mt-3 px-3 py-1 bg-[#171A20] text-white text-xs">
              推荐企业家
            </span>
          </div>

          <!-- Info Section -->
          <div class="p-8">
            <h2 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-4">个人简介</h2>
            <p v-if="entrepreneur.bio" class="text-gray-600 leading-relaxed mb-8">
              {{ entrepreneur.bio }}
            </p>
            <p v-else class="text-gray-300 mb-8">暂无简介</p>

            <!-- Contact Info -->
            <div class="border-t border-[#EEEEEE] pt-8">
              <h2 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-4">联系方式</h2>
              <div class="grid grid-cols-2 gap-6">
                <div v-if="entrepreneur.contact_phone">
                  <div class="text-xs text-gray-400 mb-1">电话</div>
                  <div class="text-gray-900">{{ entrepreneur.contact_phone }}</div>
                </div>
                <div v-if="entrepreneur.contact_email">
                  <div class="text-xs text-gray-400 mb-1">邮箱</div>
                  <div class="text-gray-900">{{ entrepreneur.contact_email }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Back Link -->
        <div class="mt-8 text-center">
          <Link href="/entrepreneurs" class="text-sm text-[#3E6AE1] hover:underline">← 返回企业家库</Link>
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

defineProps({
  entrepreneur: Object
})
</script>