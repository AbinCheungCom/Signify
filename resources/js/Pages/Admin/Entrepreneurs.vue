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
      <!-- Success/Error Messages -->
      <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-white border-l-4 border-green-500">
        <p class="text-green-700 text-sm">{{ $page.props.flash.success }}</p>
      </div>
      <div v-if="$page.props.flash.error" class="mb-6 p-4 bg-white border-l-4 border-red-500">
        <p class="text-red-700 text-sm">{{ $page.props.flash.error }}</p>
      </div>

      <!-- Filters -->
      <div class="bg-white p-4 mb-6 flex items-center space-x-3">
        <input
          v-model="search"
          type="text"
          placeholder="搜索姓名..."
          class="flex-1 px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
          @keydown.enter="doSearch"
        />
        <select v-model="status" class="px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none">
          <option value="">全部状态</option>
          <option value="pending">待审核</option>
          <option value="approved">已通过</option>
          <option value="rejected">已拒绝</option>
        </select>
        <button
          @click="doSearch"
          class="px-5 py-3 bg-[#3E6AE1] text-white text-sm font-medium rounded hover:bg-[#3558c4] transition-colors duration-300"
        >
          搜索
        </button>
        <button
          @click="resetFilters"
          class="px-5 py-3 border border-[#EEEEEE] text-gray-500 text-sm rounded hover:bg-[#F4F4F4] transition-colors duration-300"
        >
          重置
        </button>
      </div>

      <!-- Batch Actions (仅在选中了待审核项时显示) -->
      <div v-if="selectedPending.length > 0" class="bg-[#F4F4F4] p-4 mb-4 flex items-center justify-between">
        <span class="text-sm text-gray-600">已选择 {{ selectedPending.length }} 条待审核记录</span>
        <div class="flex items-center space-x-3">
          <button
            @click="batchApprove"
            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700"
          >
            批量通过
          </button>
          <button
            @click="batchReject"
            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700"
          >
            批量拒绝
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white">
        <div class="px-6 py-4 border-b border-[#EEEEEE] flex justify-between items-center">
          <h2 class="text-lg font-medium text-gray-900">企业家列表</h2>
          <span class="text-sm text-gray-400">共 {{ entrepreneurs.total }} 条</span>
        </div>

        <div v-if="entrepreneurs.data.length === 0" class="p-12 text-center text-gray-400">
          暂无记录
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-[#EEEEEE]">
                <th class="px-6 py-4 text-left">
                  <input
                    type="checkbox"
                    @change="toggleSelectAll"
                    :checked="isAllPendingSelected"
                    class="rounded border-gray-300"
                  />
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">企业家</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">状态</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">推荐</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">行业/城市</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">更新时间</th>
                <th class="px-6 py-4 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">操作</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#EEEEEE]">
              <tr v-for="entrepreneur in entrepreneurs.data" :key="entrepreneur.id" class="hover:bg-[#F4F4F4]/50">
                <td class="px-6 py-4">
                  <input
                    v-if="entrepreneur.status === 'pending'"
                    type="checkbox"
                    :value="entrepreneur.id"
                    v-model="selectedIds"
                    class="rounded border-gray-300"
                  />
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-[#F4F4F4] rounded-full overflow-hidden mr-3 flex-shrink-0">
                      <img v-if="entrepreneur.avatar" :src="`/storage/${entrepreneur.avatar}`" :alt="entrepreneur.name" class="w-full h-full object-cover" />
                      <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-sm font-light">
                        {{ entrepreneur.name.charAt(0) }}
                      </div>
                    </div>
                    <div>
                      <div class="font-medium text-gray-900">{{ entrepreneur.name }}</div>
                      <div class="text-xs text-gray-400">ID: {{ entrepreneur.id }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="text-xs font-medium px-2 py-1 rounded" :class="{
                    'bg-yellow-100 text-yellow-800': entrepreneur.status === 'pending',
                    'bg-green-100 text-green-800': entrepreneur.status === 'approved',
                    'bg-red-100 text-red-800': entrepreneur.status === 'rejected'
                  }">
                    {{ statusText(entrepreneur.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  <span v-if="entrepreneur.is_featured" class="text-yellow-500">⭐</span>
                  <span v-else class="text-gray-300">—</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ entrepreneur.industry }} / {{ entrepreneur.city }}</td>
                <td class="px-6 py-4 text-sm text-gray-400">{{ formatDate(entrepreneur.updated_at) }}</td>
                <td class="px-6 py-4 text-right">
                  <div class="flex justify-end items-center space-x-3">
                    <button v-if="entrepreneur.status === 'pending'" @click="approve(entrepreneur.id)" class="text-sm text-green-600 hover:text-green-800">通过</button>
                    <button v-if="entrepreneur.status === 'pending'" @click="reject(entrepreneur.id)" class="text-sm text-red-600 hover:text-red-800">拒绝</button>
                    <button @click="toggleFeatured(entrepreneur.id)" class="text-sm text-yellow-600 hover:text-yellow-800">
                      {{ entrepreneur.is_featured ? '取消推荐' : '设为推荐' }}
                    </button>
                    <Link :href="`/entrepreneurs/${entrepreneur.id}`" target="_blank" class="text-sm text-gray-400 hover:text-gray-600">查看</Link>
                    <button @click="destroy(entrepreneur.id)" class="text-sm text-gray-300 hover:text-red-600">删除</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="entrepreneurs.last_page > 1" class="px-6 py-4 border-t border-[#EEEEEE] flex justify-center">
          <div class="flex items-center space-x-4">
            <Link
              v-if="entrepreneurs.current_page > 1"
              :href="paginationUrl(entrepreneurs.current_page - 1)"
              class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900"
            >
              上一页
            </Link>
            <span class="text-sm text-gray-400">第 {{ entrepreneurs.current_page }} / {{ entrepreneurs.last_page }} 页</span>
            <Link
              v-if="entrepreneurs.current_page < entrepreneurs.last_page"
              :href="paginationUrl(entrepreneurs.current_page + 1)"
              class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900"
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
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  entrepreneurs: Object,
  filters: Object
})

const search = ref(props.filters.search || '')
const status = ref(props.filters.status || '')
const selectedIds = ref([])

const statusText = (s) => ({ pending: '待审核', approved: '已通过', rejected: '已拒绝' }[s] || s)
const formatDate = (d) => new Date(d).toLocaleDateString('zh-CN')

const paginationUrl = (page) => `/admin/entrepreneurs?page=${page}&search=${search.value}&status=${status.value}`

const isAllPendingSelected = computed(() => {
  const pendingIds = props.entrepreneurs.data
    .filter(e => e.status === 'pending')
    .map(e => e.id)
  return pendingIds.length > 0 && pendingIds.every(id => selectedIds.value.includes(id))
})

const selectedPending = computed(() =>
  props.entrepreneurs.data.filter(e => e.status === 'pending' && selectedIds.value.includes(e.id))
)

const toggleSelectAll = (e) => {
  const pendingIds = props.entrepreneurs.data
    .filter(em => em.status === 'pending')
    .map(em => em.id)
  if (e.target.checked) {
    selectedIds.value = [...new Set([...selectedIds.value, ...pendingIds])]
  } else {
    selectedIds.value = selectedIds.value.filter(id => !pendingIds.includes(id))
  }
}

const doSearch = () => router.get(`/admin/entrepreneurs?search=${search.value}&status=${status.value}`)

const resetFilters = () => {
  search.value = ''
  status.value = ''
  router.get('/admin/entrepreneurs')
}

const approve = (id) => router.post(`/admin/entrepreneurs/${id}/approve`)
const reject = (id) => router.post(`/admin/entrepreneurs/${id}/reject`)
const toggleFeatured = (id) => router.post(`/admin/entrepreneurs/${id}/toggle-featured`)

const batchApprove = () => {
  selectedIds.value.forEach(id => router.post(`/admin/entrepreneurs/${id}/approve`))
  selectedIds.value = []
}

const batchReject = () => {
  selectedIds.value.forEach(id => router.post(`/admin/entrepreneurs/${id}/reject`))
  selectedIds.value = []
}

const destroy = (id) => {
  if (confirm('确定要删除这条记录吗？')) {
    router.delete(`/admin/entrepreneurs/${id}`)
  }
}
</script>