<template>
  <div class="min-h-screen bg-gray-50">
    <!-- 导航栏 -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold text-gray-900">Signify</h1>
          </div>
          <div class="flex items-center space-x-4">
            <Link href="/entrepreneurs" class="text-gray-600 hover:text-gray-900">企业家库</Link>
            <Link v-if="$page.props.auth.user" href="/my/profile" class="text-gray-600 hover:text-gray-900">个人中心</Link>
            <template v-else>
              <Link href="/login" class="text-gray-600 hover:text-gray-900">登录</Link>
              <Link href="/register" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">注册</Link>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- 英雄区域 -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">企业家形象资产数字化</h2>
          <p class="text-lg text-gray-600 mb-8">安全、可控、专业的企业家形象管理平台</p>
          <Link href="/entrepreneurs" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
            浏览企业家库 →
          </Link>
        </div>
      </div>
    </div>

    <!-- 推荐企业家 -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <h3 class="text-2xl font-bold text-gray-900 mb-6">推荐企业家</h3>

      <div v-if="featuredEntrepreneurs.length === 0" class="text-center py-12 text-gray-500">
        暂无推荐企业家
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="entrepreneur in featuredEntrepreneurs"
          :key="entrepreneur.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition"
        >
          <Link :href="`/entrepreneurs/${entrepreneur.id}`">
            <div class="aspect-square bg-gray-200 relative">
              <img
                v-if="entrepreneur.avatar"
                :src="`/storage/${entrepreneur.avatar}`"
                :alt="entrepreneur.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-4xl font-bold">
                {{ entrepreneur.name.charAt(0) }}
              </div>
            </div>
            <div class="p-4">
              <h4 class="text-lg font-semibold text-gray-900">{{ entrepreneur.name }}</h4>
              <p class="text-sm text-gray-600 mt-1">{{ entrepreneur.industry }} · {{ entrepreneur.city }}</p>
              <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ entrepreneur.bio }}</p>
            </div>
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  featuredEntrepreneurs: {
    type: Array,
    default: () => []
  }
})
</script>
