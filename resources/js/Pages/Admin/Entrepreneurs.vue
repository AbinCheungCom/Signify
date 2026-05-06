<template>
  <div class="min-h-screen bg-gray-100">
    <!-- 管理导航 -->
    <nav class="bg-indigo-600 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center">
            <Link href="/admin/dashboard" class="text-lg font-bold">Signify 管理后台</Link>
            <div class="ml-10 flex space-x-4">
              <Link href="/admin/dashboard" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-indigo-500">
                待审核
              </Link>
              <Link href="/admin/entrepreneurs" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-indigo-500" :class="$page.url === '/admin/entrepreneurs' ? 'bg-indigo-700' : ''">
                全部企业家
              </Link>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <Link href="/" class="text-sm hover:text-gray-200">返回首页</Link>
          </div>
        </div>
      </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- 消息提示 -->
      <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
        <p class="text-green-700">{{ $page.props.flash.success }}</p>
      </div>

      <!-- 筛选 -->
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="md:col-span-2">
            <input
              v-model="search"
              type="text"
              placeholder="搜索姓名..."
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              @keydown.enter="doSearch"
            />
          </div>
          <select v-model="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">全部状态</option>
            <option value="pending">待审核</option>
            <option value="approved">已通过</option>
            <option value="rejected">已拒绝</option>
          </select>
          <button @click="doSearch" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            搜索
          </button>
        </div>
      </div>

      <!-- 列表 -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center">
          <h2 class="font-semibold text-gray-900">企业家列表</h2>
          <span class="text-sm text-gray-500">共 {{ entrepreneurs.total }} 条</span>
        </div>

        <div v-if="entrepreneurs.data.length === 0" class="p-6 text-center text-gray-500">
          暂无记录
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">企业家</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">状态</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">推荐</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">行业/城市</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">更新时间</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">操作</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="entrepreneur in entrepreneurs.data" :key="entrepreneur.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-200 rounded-full overflow-hidden mr-3 flex-shrink-0">
                      <img v-if="entrepreneur.avatar" :src="`/storage/${entrepreneur.avatar}`" :alt="entrepreneur.name" class="w-full h-full object-cover" />
                      <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-sm font-bold">
                        {{ entrepreneur.name.charAt(0) }}
                      </div>
                    </div>
                    <div>
                      <div class="font-medium text-gray-900">{{ entrepreneur.name }}</div>
                      <div class="text-xs text-gray-500">ID: {{ entrepreneur.id }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-2 py-1 text-xs font-medium rounded"
                    :class="{
                      'bg-yellow-100 text-yellow-800': entrepreneur.status === 'pending',
                      'bg-green-100 text-green-800': entrepreneur.status === 'approved',
                      'bg-red-100 text-red-800': entrepreneur.status === 'rejected'
                    }"
                  >
                    {{ statusText(entrepreneur.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="entrepreneur.is_featured" class="text-yellow-500">⭐ 是</span>
                  <span v-else class="text-gray-400">否</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ entrepreneur.industry }} / {{ entrepreneur.city }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(entrepreneur.updated_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                  <div class="flex justify-end space-x-2">
                    <button
                      v-if="entrepreneur.status === 'pending'"
                      @click="approve(entrepreneur.id)"
                      class="text-green-600 hover:text-green-800"
                    >
                      通过
                    </button>
                    <button
                      v-if="entrepreneur.status === 'pending'"
                      @click="reject(entrepreneur.id)"
                      class="text-red-600 hover:text-red-800"
                    >
                      拒绝
                    </button>
                    <button
                      @click="toggleFeatured(entrepreneur.id)"
                      class="text-yellow-600 hover:text-yellow-800"
                    >
                      {{ entrepreneur.is_featured ? '取消推荐' : '设为推荐' }}
                    </button>
                    <Link
                      :href="`/entrepreneurs/${entrepreneur.id}`"
                      target="_blank"
                      class="text-indigo-600 hover:text-indigo-800"
                    >
                      查看
                    </Link>
                    <button
                      @click="destroy(entrepreneur.id)"
                      class="text-gray-400 hover:text-red-600"
                    >
                      删除
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 分页 -->
        <div v-if="entrepreneurs.last_page > 1" class="px-6 py-4 border-t flex justify-center">
          <div class="flex space-x-2">
            <Link
              v-if="entrepreneurs.current_page > 1"
              :href="`/admin/entrepreneurs?page=${entrepreneurs.current_page - 1}&status=${status}&search=${search}`"
              class="px-3 py-1 border rounded hover:bg-gray-50"
            >
              上一页
            </Link>
            <span class="px-3 py-1">第 {{ entrepreneurs.current_page }} / {{ entrepreneurs.last_page }} 页</span>
            <Link
              v-if="entrepreneurs.current_page < entrepreneurs.last_page"
              :href="`/admin/entrepreneurs?page=${entrepreneurs.current_page + 1}&status=${status}&search=${search}`"
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
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  entrepreneurs: Object,
  filters: Object
})

const search = ref(props.filters.search || '')
const status = ref(props.filters.status || '')

const statusText = (s) => ({ pending: '待审核', approved: '已通过', rejected: '已拒绝' }[s] || s)

const formatDate = (d) => new Date(d).toLocaleDateString('zh-CN')

const doSearch = () => {
  router.get(`/admin/entrepreneurs?search=${search.value}&status=${status.value}`)
}

const approve = (id) => router.post(`/admin/entrepreneurs/${id}/approve`)
const reject = (id) => router.post(`/admin/entrepreneurs/${id}/reject`)
const toggleFeatured = (id) => router.post(`/admin/entrepreneurs/${id}/toggle-featured`)
const destroy = (id) => {
  if (confirm('确定要删除这条记录吗？')) {
    router.delete(`/admin/entrepreneurs/${id}`)
  }
}
</script>
