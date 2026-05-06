<template>
  <div class="font-sans antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/75 backdrop-blur-md">
      <div class="max-w-7xl mx-auto px-8 h-16 flex items-center justify-between">
        <Link href="/" class="text-xl font-medium tracking-tight text-gray-900">Signify</Link>
        <div class="flex items-center space-x-8">
          <Link href="/entrepreneurs" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-300">企业家库</Link>
          <template v-if="$page.props.auth.user">
            <Link href="/my/profile" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-300">个人中心</Link>
            <Link href="/admin/dashboard" v-if="$page.props.auth.user.is_admin" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-300">管理后台</Link>
          </template>
          <template v-else>
            <Link href="/login" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-300">登录</Link>
            <Link href="/register" class="px-4 py-2 bg-[#3E6AE1] text-white text-sm font-medium rounded hover:bg-[#3558c4] transition-colors duration-300">注册</Link>
          </template>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="min-h-screen bg-gray-100 flex items-center justify-center relative">
      <div class="text-center px-8">
        <h1 class="text-5xl font-medium text-gray-900 tracking-tight mb-4">企业家形象资产数字化</h1>
        <p class="text-lg text-gray-500 mb-8">安全、可控、专业的企业家形象管理平台</p>
        <Link href="/entrepreneurs" class="inline-block px-8 py-3 bg-[#3E6AE1] text-white font-medium rounded hover:bg-[#3558c4] transition-colors duration-300">
          浏览企业家库 →
        </Link>
      </div>
    </section>

    <!-- Featured Section -->
    <section class="py-24 bg-white">
      <div class="max-w-7xl mx-auto px-8">
        <h2 class="text-3xl font-medium text-gray-900 mb-12">推荐企业家</h2>

        <div v-if="featuredEntrepreneurs.length === 0" class="text-center py-20 text-gray-400">
          暂无推荐企业家
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <Link
            v-for="entrepreneur in featuredEntrepreneurs"
            :key="entrepreneur.id"
            :href="`/entrepreneurs/${entrepreneur.id}`"
            class="group block"
          >
            <div class="aspect-square bg-gray-100 overflow-hidden mb-4">
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
            <h3 class="text-lg font-medium text-gray-900">{{ entrepreneur.name }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ entrepreneur.industry }} · {{ entrepreneur.city }}</p>
          </Link>
        </div>

        <div class="mt-16 text-center">
          <Link href="/entrepreneurs" class="text-[#3E6AE1] text-sm font-medium hover:underline">
            查看全部企业家 →
          </Link>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-[#F4F4F4]">
      <div class="max-w-7xl mx-auto px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
          <div>
            <div class="text-3xl mb-4">◈</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">数据隔离</h3>
            <p class="text-sm text-gray-500 leading-relaxed">用户仅可修改自己的企业家档案，通过 user_id 字段与严格权限验证确保数据安全。</p>
          </div>
          <div>
            <div class="text-3xl mb-4">◈</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">认证审批</h3>
            <p class="text-sm text-gray-500 leading-relaxed">完整的管理员后台，支持审批/拒绝/推荐企业家档案，保障平台内容质量。</p>
          </div>
          <div>
            <div class="text-3xl mb-4">◈</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">形象管理</h3>
            <p class="text-sm text-gray-500 leading-relaxed">支持头像上传、个人简介、行业城市等全方位展示，让企业家形象更加专业。</p>
          </div>
        </div>
      </div>
    </section>

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
  featuredEntrepreneurs: {
    type: Array,
    default: () => []
  }
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
</style>