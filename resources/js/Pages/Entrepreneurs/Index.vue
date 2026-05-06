<template>
  <div class="font-sans antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/75 backdrop-blur-md">
      <div class="max-w-7xl mx-auto px-8 h-16 flex items-center justify-between">
        <Link href="/" class="text-xl font-medium tracking-tight text-gray-900">Signify</Link>
        <div class="flex items-center space-x-8">
          <Link href="/entrepreneurs" class="text-sm font-medium text-[#3E6AE1]">企业家库</Link>
          <template v-if="$page.props.auth.user">
            <Link href="/my/profile" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-300">个人中心</Link>
          </template>
          <template v-else>
            <Link href="/login" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-300">登录</Link>
          </template>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-24 pb-16 bg-[#F4F4F4] min-h-screen">
      <div class="max-w-7xl mx-auto px-8">
        <div class="mb-12">
          <h1 class="text-4xl font-medium text-gray-900 tracking-tight">企业家库</h1>
          <p class="text-gray-500 mt-2">探索优秀企业家的形象档案</p>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 mb-8 flex items-center space-x-4">
          <input
            v-model="search"
            type="text"
            placeholder="搜索姓名、行业..."
            class="flex-1 px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none focus:ring-2 focus:ring-[#3E6AE1]/20"
            @keydown.enter="filter"
          />
          <select
            v-model="selectedIndustry"
            class="px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none"
          >
            <option value="">全部行业</option>
            <option v-for="industry in industries" :key="industry" :value="industry">{{ industry }}</option>
          </select>
          <select
            v-model="selectedCity"
            class="px-4 py-3 bg-[#F4F4F4] rounded text-sm focus:outline-none"
          >
            <option value="">全部城市</option>
            <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
          </select>
          <button
            @click="filter"
            class="px-6 py-3 bg-[#3E6AE1] text-white text-sm font-medium rounded hover:bg-[#3558c4] transition-colors duration-300"
          >
            筛选
          </button>
        </div>

        <!-- Count -->
        <div class="text-sm text-gray-400 mb-6">共 {{ entrepreneurs.total }} 位企业家</div>

        <!-- Grid -->
        <div v-if="entrepreneurs.data.length === 0" class="text-center py-24 text-gray-400">
          暂无符合条件的记录
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <Link
            v-for="entrepreneur in entrepreneurs.data"
            :key="entrepreneur.id"
            :href="`/entrepreneurs/${entrepreneur.id}`"
            class="group block bg-white"
          >
            <div class="aspect-square bg-gray-100 overflow-hidden relative">
              <img
                v-if="entrepreneur.avatar"
                :src="`/storage/${entrepreneur.avatar}`"
                :alt="entrepreneur.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-gray-300 text-6xl font-light">
                {{ entrepreneur.name.charAt(0) }}
              </div>
              <span
                v-if="entrepreneur.is_featured"
                class="absolute top-4 right-4 px-2 py-1 bg-[#171A20] text-white text-xs"
              >
                推荐
              </span>
            </div>
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900">{{ entrepreneur.name }}</h3>
              <p class="text-sm text-gray-500 mt-1">{{ entrepreneur.industry }} · {{ entrepreneur.city }}</p>
              <p v-if="entrepreneur.bio" class="text-sm text-gray-400 mt-3 line-clamp-2">{{ entrepreneur.bio }}</p>
            </div>
          </Link>
        </div>

        <!-- Pagination -->
        <div v-if="entrepreneurs.last_page > 1" class="mt-16 flex justify-center items-center space-x-4">
          <Link
            v-if="entrepreneurs.current_page > 1"
            :href="`/entrepreneurs?page=${entrepreneurs.current_page - 1}&search=${search}&industry=${selectedIndustry}&city=${selectedCity}`"
            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors duration-300"
          >
            上一页
          </Link>
          <span class="text-sm text-gray-400">第 {{ entrepreneurs.current_page }} / {{ entrepreneurs.last_page }} 页</span>
          <Link
            v-if="entrepreneurs.current_page < entrepreneurs.last_page"
            :href="`/entrepreneurs?page=${entrepreneurs.current_page + 1}&search=${search}&industry=${selectedIndustry}&city=${selectedCity}`"
            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors duration-300"
          >
            下一页
          </Link>
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