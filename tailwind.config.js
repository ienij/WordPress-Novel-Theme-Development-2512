/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
    "./**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f9f9f9',
          100: '#f0f0f0',
          500: '#666666',
          900: '#1a1a1a'
        }
      },
      fontFamily: {
        'reading': ['Georgia', 'Times New Roman', 'serif'],
        'ui': ['Inter', 'system-ui', 'sans-serif']
      },
      aspectRatio: {
        '3/4': '3 / 4',
      }
    },
  },
  plugins: [],
};