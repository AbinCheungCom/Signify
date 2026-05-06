import { createApp } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'vite';

// Import CSS
import './css/app.css';

createInertiaApp({
  title: (title) => title ? `${title} - Signify` : 'Signify',
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) }).use(plugin).mount(el);
  },
  progress: {
    color: '#3E6AE1',
  },
});