import resolve from '@rollup/plugin-node-resolve';

export default {
    input: 'js/main.js',
    output: {
        file: 'public/js/bundle.js',
        format: 'iife',
        name: 'App'
    },
    plugins: [
        resolve()
    ],
    context: 'window'
};