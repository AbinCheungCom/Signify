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
            <Link href="/entrepreneurs" class="text-indigo-600 font-medium">企业家库</Link>
            <Link v-if="$page.props.auth.user" href="/my/profile" class="text-gray-600 hover:text-gray-900">个人中心</Link>
            <template v-else>
              <Link href="/login" class="text-gray-600 hover:text-gray-900">登录</Link>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- 主内容 -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">企业家库</h1>
      </div>

      <!-- 搜索与筛选 -->
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="md:col-span-2">
            <input
              v-model="search"
              type="text"
              placeholder="搜索姓名、行业..."
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              @keydown.enter="filter"
            />
          </div>
          <select
            v-model="selectedIndustry"
            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          >
            <option value="">全部行业</option>
            <option v-for="industry in industries" :key="industry" :value="industry">
              {{ industry }}
            </option>
          </select>
          <select
            v-model="selectedCity"
            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          >
            <option value="">全部城市</option>
            <option v-for="city in cities" :key="city" :value="city">
              {{ city }}
            </option>
          </select>
        </div>
        <div class="mt-4 flex justify-between items-center">
          <button
            @click="filter"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
          >
            筛选
          </button>
          <span class="text-sm text-gray-500">
            共 {{ entrepreneurs.total }} 位企业家
          </span>
        </div>
      </div>

      <!-- 列表 -->
      <div v-if="entrepreneurs.data.length === 0" class="text-center py-12 text-gray-500">
        暂无符合条件的记录
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="entrepreneur in entrepreneurs.data"
          :key="entrepreneur.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition"
        >
          <Link :href="`/entrepreneurs/${entrepreneur.id}`" class="block">
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
              <span
                v-if="entrepreneur.is_featured"
                class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-xs px-2 py-1 rounded"
              >
                推荐
              </span>
            </div>
            <div class="p-4">
              <h4 class="text-lg font-semibold text-gray-900">{{ entrepreneur.name }}</h4>
              <p class="text-sm text-gray-600 mt-1">{{ entrepreneur.industry }} · {{ entrepreneur.city }}</p>
              <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ entrepreneur.bio }}</p>
            </div>
          </Link>
        </div>
      </div>

      <!-- 分页 -->
      <div v-if="entrepreneurs.last_page > 1" class="mt-6 flex justify-center">
        <div class="flex space-x-2">
          <Link
            v-if="entrepreneurs.current_page > 1"
            :href="`/entrepreneurs?page=${entrepreneurs.current_page - 1}&search=${search}&industry=${selectedIndustry}&city=${selectedCity}`"
            class="px-3 py-1 rounded border hover:bg-gray-50"
          >
            上一页
          </Link>
          <span class="px-3 py-1">
            第 {{ entrepreneurs.current_page }} / {{ entrepreneurs.last_page }} 页
          </span>
          <Link
            v-if="entrepreneurs.current_page < entrepreneurs.last_page"
            :href="`/entrepreneurs?page=${entrepreneurs.current_page + 1}&search=${search}&industry=${selectedIndustry}&city=${selectedCity}`"
            class="px-3 py-1 rounded border hover:bg-gray-50"
          >
            下一页
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  entrepreneurs: Object,
  industries: Array,
  cities: Array,
  filters: Object
})

const search = ref(props.filters.search || '')
const selectedIndustry = ref(props.filters.industry || '')
const selectedCity = ref(props.filters.city || '')

const filter = () => {
  const params = new URLSearchParams()
  if (search.value) params.append('search', search.value)
  if (selectedIndustry.value) params.append('industry', selectedIndustry.value)
  if (selectedCity.value) params.append('city', selectedCity.value)
  window.location.href = `/entrepreneurs?${params.toString()}`
}
</script>
