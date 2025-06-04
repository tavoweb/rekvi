// assets/js/main.js
import '@material/web/button/filled-button.js';
import '@material/web/button/outlined-button.js';
import '@material/web/textfield/outlined-text-field.js';
import '@material/web/list/list.js';
import '@material/web/list/list-item.js';
import '@material/web/divider/divider.js';
import '@material/web/iconbutton/icon-button.js';
import '@material/web/icon/icon.js';

// For now, we'll also import the typography styles here
// In a more advanced setup, these might be handled differently (e.g., loaded once)
import { styles as typescaleStyles } from '@material/web/typography/md-typescale-styles.js';

// Apply typography styles to the document
if (typescaleStyles && typescaleStyles.styleSheet) {
  document.adoptedStyleSheets.push(typescaleStyles.styleSheet);
} else {
  console.warn('Material Web typescale styles not loaded.');
}

console.log('Material Web components (button, text-field, list, list-item, divider, icon-button, icon) and typography imported.');
