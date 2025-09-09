/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // 👈 esto habilita el toggle manual
    content: [
      "./resources/**/*.blade.php",
      "./resources/**/*.js",
      "./resources/**/*.vue",
    ],
    theme: {
      extend: {},
    },
    plugins: [],
  }
  