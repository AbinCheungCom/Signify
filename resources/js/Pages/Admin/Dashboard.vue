<template>
  <div class="font-sans antialiased bg-[#F4F4F4] min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white border-b border-[#EEEEEE]">
      <div class="max-w-7xl mx-auto px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center">
            <Link href="/admin/dashboard" class="text-lg font-medium text-gray-900 tracking-tight">Signify</Link>
            <div class="ml-10 flex items-center space-x-1">
              <Link
                href="/admin/dashboard"
                class="px-4 py-2 text-sm font-medium rounded transition-colors duration-300"
                :class="$page.url === '/admin/dashboard' ? 'bg-[#F4F4F4] text-gray-900' : 'text-gray-500 hover:text-gray-900'"
              >
                待审核
              </Link>
              <Link
                href="/admin/entrepreneurs"
                class="px-4 py-2 text-sm font-medium rounded transition-colors duration-300"
                :class="$page.url === '/admin/entrepreneurs' ? 'bg-[#F4F4F4] text-gray-900' : 'text-gray-500 hover:text-gray-900'"
              >
                全部企业家
              </Link>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <Link href="/" class="text-sm text-gray-500 hover:text-gray-900">返回首页</Link>
          </div>
        </div>
      </div>
    </nav>

    <div class="max-w-7xl mx-auto px-8 py-10">
      <!-- Stats -->
      <div class="grid grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6">
          <div class="text-3xl font-medium text-gray-900">{{ stats.total }}</div>
          <div class="text-sm text-gray-400 mt-1">全部企业家</div>
        </div>
        <div class="bg-white p-6">
          <div class="text-3xl font-medium text-yellow-600">{{ stats.pending }}</div>
          <div class="text-sm text-gray-400 mt-1">待审核</div>
        </div>
        <div class="bg-white p-6">
          <div class="text-3xl font-medium text-green-600">{{ stats.approved }}</div>
          <div class="text-sm text-gray-400 mt-1">已通过</div>
        </div>
        <div class="bg-white p-6">
          <div class="text-3xl font-medium text-red-600">{{ stats.rejected }}</div>
          <div class="text-sm text-gray-400 mt-1">已拒绝</div>
        </div>
      </div>

      <!-- Success Message -->
      <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-white border-l-4 border-green-500">
        <p class="text-green-700 text-sm">{{ $page.props.flash.success }}</p>
      </div>

      <!-- Pending List -->
      <div class="bg-white">
        <div class="px-6 py-4 border-b border-[#EEEEEE]">
          <h2 class="text-lg font-medium text-gray-900">待审核申请</h2>
        </div>

        <div v-if="pending.data.length === 0" class="p-12 text-center text-gray-400">
          暂无待审核的申请
        </div>

        <div v-else class="divide-y divide-[#EEEEEE]">
          <div v-for="entrepreneur in pending.data" :key="entrepreneur.id" class="px-6 py-5 flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-[#F4F4F4] rounded-full overflow-hidden mr-5 flex-shrink-0">
                <img v-if="entrepreneur.avatar" :src="`/storage/${entrepreneur.avatar}`" :alt="entrepreneur.name" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-lg font-light">
                  {{ entrepreneur.name.charAt(0) }}
                </div>
              </div>
              <div>
                <div class="font-medium text-gray-900">{{ entrepreneur.name }}</div>
                <div class="text-sm text-gray-400 mt-0.5">{{ entrepreneur.industry }} · {{ entrepreneur.city }}</div>
                <div v-if="entrepreneur.bio" class="text-sm text-gray-300 mt-1 truncate max-w-md">{{ entrepreneur.bio }}</div>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <button
                @click="approve(entrepreneur.id)"
                class="px-4 py-2 bg-[#3E6AE1] text-white text-sm font-medium rounded hover:bg-[#3558c4] transition-colors duration-300"
              >
                通过
              </button>
              <button
                @click="reject(entrepreneur.id)"
                class="px-4 py-2 border border-gray-200 text-gray-600 text-sm font-medium rounded hover:bg-[#F4F4F4] transition-colors duration-300"
              >
                拒绝
              </button>
              <Link
                :href="`/entrepreneurs/${entrepreneur.id}`"
                target="_blank"
                class="px-4 py-2 text-sm text-gray-400 hover:text-gray-600 transition-colors duration-300"
              >
                查看
              </Link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pending.last_page > 1" class="px-6 py-4 border-t border-[#EEEEEE] flex justify-center">
          <div class="flex items-center space-x-4">
            <Link
              v-if="pending.current_page > 1"
              :href="`/admin/dashboard?page=${pending.current_page - 1}`"
              class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors duration-300"
            >
              上一页
            </Link>
            <span class="text-sm text-gray-400">第 {{ pending.current_page }} / {{ pending.last_page }} 页</span>
            <Link
              v-if="pending.current_page < pending.last_page"
              :href="`/admin/dashboard?page=${pending.current_page + 1}`"
              class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors duration-300"
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

const approve = (id) => router.post(`/admin/entrepreneurs/${id}/approve`)
const reject = (id) => router.post(`/admin/entrepreneurs/${id}/reject`)
</script>