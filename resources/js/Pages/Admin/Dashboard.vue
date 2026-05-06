<template>
  <div class="min-h-screen bg-gray-100">
    <!-- 管理导航 -->
    <nav class="bg-indigo-600 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center">
            <Link href="/admin/dashboard" class="text-lg font-bold">Signify 管理后台</Link>
            <div class="ml-10 flex space-x-4">
              <Link href="/admin/dashboard" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-indigo-500" :class="$page.url === '/admin/dashboard' ? 'bg-indigo-700' : ''">
                待审核
              </Link>
              <Link href="/admin/entrepreneurs" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-indigo-500">
                全部企业家
              </Link>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <Link href="/" class="text-sm hover:text-gray-200">返回首页</Link>
            <span class="text-sm">管理员</span>
          </div>
        </div>
      </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- 统计卡片 -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="text-2xl font-bold text-gray-900">{{ stats.total }}</div>
          <div class="text-sm text-gray-500">全部企业家</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</div>
          <div class="text-sm text-gray-500">待审核</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="text-2xl font-bold text-green-600">{{ stats.approved }}</div>
          <div class="text-sm text-gray-500">已通过</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="text-2xl font-bold text-red-600">{{ stats.rejected }}</div>
          <div class="text-sm text-gray-500">已拒绝</div>
        </div>
      </div>

      <!-- 消息提示 -->
      <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
        <p class="text-green-700">{{ $page.props.flash.success }}</p>
      </div>

      <!-- 待审核列表 -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
          <h2 class="text-lg font-semibold text-gray-900">待审核申请</h2>
        </div>

        <div v-if="pending.data.length === 0" class="p-6 text-center text-gray-500">
          暂无待审核的申请
        </div>

        <div v-else class="divide-y">
          <div v-for="entrepreneur in pending.data" :key="entrepreneur.id" class="p-6 flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-gray-200 rounded-full overflow-hidden mr-4 flex-shrink-0">
                <img v-if="entrepreneur.avatar" :src="`/storage/${entrepreneur.avatar}`" :alt="entrepreneur.name" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-lg font-bold">
                  {{ entrepreneur.name.charAt(0) }}
                </div>
              </div>
              <div>
                <div class="font-medium text-gray-900">{{ entrepreneur.name }}</div>
                <div class="text-sm text-gray-500">{{ entrepreneur.industry }} · {{ entrepreneur.city }}</div>
                <div v-if="entrepreneur.bio" class="text-sm text-gray-400 mt-1 line-clamp-1">{{ entrepreneur.bio }}</div>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <button
                @click="approve(entrepreneur.id)"
                class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700"
              >
                通过
              </button>
              <button
                @click="reject(entrepreneur.id)"
                class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700"
              >
                拒绝
              </button>
              <Link
                :href="`/entrepreneurs/${entrepreneur.id}`"
                target="_blank"
                class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50"
              >
                查看
              </Link>
            </div>
          </div>
        </div>

        <!-- 分页 -->
        <div v-if="pending.last_page > 1" class="px-6 py-4 border-t flex justify-center">
          <div class="flex space-x-2">
            <Link
              v-if="pending.current_page > 1"
              :href="`/admin/dashboard?page=${pending.current_page - 1}`"
              class="px-3 py-1 border rounded hover:bg-gray-50"
            >
              上一页
            </Link>
            <span class="px-3 py-1">第 {{ pending.current_page }} / {{ pending.last_page }} 页</span>
            <Link
              v-if="pending.current_page < pending.last_page"
              :href="`/admin/dashboard?page=${pending.current_page + 1}`"
              class="px-3 py-1 border rounded hover:bg-gray-50"
            >
              下一页
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

defineProps({
  pending: Object,
  stats: Object
})

const approve = (id) => {
  router.post(`/admin/entrepreneurs/${id}/approve`)
}

const reject = (id) => {
  router.post(`/admin/entrepreneurs/${id}/reject`)
}
</script>
