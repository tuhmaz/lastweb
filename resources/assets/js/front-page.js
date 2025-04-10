/**
 * Front Page
 */

'use strict';

// Initialize front page specific features
document.addEventListener('DOMContentLoaded', function () {
  // Initialize menu
  const menu = document.querySelector('#menu-1');
  if (menu) {
    new Menu(menu);
  }

  // Initialize any other front page specific features
  // Your front page specific code here
});
