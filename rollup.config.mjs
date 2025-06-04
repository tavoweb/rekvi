import resolve from '@rollup/plugin-node-resolve';
import terser from '@rollup/plugin-terser';

export default {
  input: 'assets/js/main.js', // Our new entry point
  output: {
    file: 'public/js/bundle.js', // Where the bundled JS will be saved
    format: 'esm', // ES module format, suitable for modern browsers
    sourcemap: true // Good for debugging
  },
  plugins: [
    resolve(), // Helps Rollup find external modules
    terser()   // Minifies the output bundle
  ]
};
