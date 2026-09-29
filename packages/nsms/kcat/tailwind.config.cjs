module.exports = {
    content: [require('path').join(__dirname, 'resources/views/**/*.blade.php')],
    theme: { extend: {} },
    plugins: [require('@tailwindcss/forms')],
};
