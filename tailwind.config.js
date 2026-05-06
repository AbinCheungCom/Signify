/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.vue',
    './resources/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        'electric-blue': '#3E6AE1',
        'carbon-dark': '#171A20',
        'graphite': '#393C41',
        'pewter': '#5C5E62',
        'silver-fog': '#8E8E8E',
        'cloud-gray': '#EEEEEE',
        'light-ash': '#F4F4F4',
      },
      fontFamily: {
        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
      },
    },
  },
  plugins: [],
}