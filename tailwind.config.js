/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
    ],
    theme: {
        extend: {
            colors: {
                'logi-blue': '#04A5E5',
                'logi-blue-dark': '#026d97',
                'logi-dark': '#211F21',
                'logi-white': '#F4F3F4',
                'logi-black': '#080608',
                'sbs-green-dark': '#38661d',
                'sbs-green': '#66B933'
            }
        },
    },
    plugins: [],
}