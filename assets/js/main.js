// assets/js/main.js
import '@material/web/button/filled-button.js';
import '@material/web/button/outlined-button.js';
import '@material/web/textfield/outlined-text-field.js';
import '@material/web/list/list.js';
import '@material/web/list/list-item.js';
import '@material/web/divider/divider.js';
import '@material/web/iconbutton/icon-button.js';
import '@material/web/icon/icon.js';
import '@material/web/menu/menu.js';
import '@material/web/menu/menu-item.js';

// For now, we'll also import the typography styles here
// In a more advanced setup, these might be handled differently (e.g., loaded once)
import { styles as typescaleStyles } from '@material/web/typography/md-typescale-styles.js';

// Apply typography styles to the document
if (typescaleStyles && typescaleStyles.styleSheet) {
  document.adoptedStyleSheets.push(typescaleStyles.styleSheet);
} else {
  console.warn('Material Web typescale styles not loaded.');
}

// DOMContentLoaded listener for various initializations
document.addEventListener('DOMContentLoaded', () => {
    // Scroll behavior for Top App Bar
    const topAppBar = document.querySelector('.md3-top-app-bar');
    if (topAppBar) {
        const handleScroll = () => {
            if (window.scrollY > 0) {
                topAppBar.classList.add('md3-top-app-bar-scrolled');
            } else {
                topAppBar.classList.remove('md3-top-app-bar-scrolled');
            }
        };

        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(handleScroll, 50);
        }, { passive: true });
        handleScroll(); // Initial check
    }

    // Overflow Menu for Top App Bar
    const overflowMenuButton = document.getElementById('overflowMenuButton');
    const overflowMenu = document.getElementById('overflowMenu');

    if (overflowMenuButton && overflowMenu) {
        overflowMenuButton.addEventListener('click', () => {
            overflowMenu.open = !overflowMenu.open;
        });
    }
});

console.log('Material Web components and behaviors (Top App Bar scroll, Overflow Menu) initialized.');
