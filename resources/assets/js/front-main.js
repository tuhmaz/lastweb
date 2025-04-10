'use strict';

window.isRtl = window.Helpers.isRtl();
window.isDarkStyle = window.Helpers.isDarkStyle();

document.addEventListener('DOMContentLoaded', function () {
  const menu = document.getElementById('navbarSupportedContent');
  const nav = document.querySelector('.layout-navbar');
  const navItemLink = document.querySelectorAll('.navbar-nav .nav-link');

  // Initialised custom options if checked
  setTimeout(function () {
    window.Helpers.initCustomOptionCheck();
  }, 1000);

  if (typeof Waves !== 'undefined') {
    Waves.init();
    Waves.attach(".btn[class*='btn-']:not([class*='btn-outline-']):not([class*='btn-label-'])", ['waves-light']);
    Waves.attach("[class*='btn-outline-']");
    Waves.attach("[class*='btn-label-']");
    Waves.attach('.pagination .page-item .page-link');
  }

  // Init BS Tooltip
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Accordion active class
  const accordionActiveFunction = function (e) {
    if (e.type === 'show.bs.collapse') {
      e.target.closest('.accordion-item').classList.add('active');
    } else if (e.type === 'hide.bs.collapse') {
      e.target.closest('.accordion-item').classList.remove('active');
    }
  };

  const accordionTriggerList = [].slice.call(document.querySelectorAll('.accordion'));
  accordionTriggerList.map(function (accordionTriggerEl) {
    accordionTriggerEl.addEventListener('show.bs.collapse', accordionActiveFunction);
    accordionTriggerEl.addEventListener('hide.bs.collapse', accordionActiveFunction);
  });

  // If layout is RTL add .dropdown-menu-end class to .dropdown-menu
  if (window.isRtl) {
    Helpers._addClass('dropdown-menu-end', document.querySelectorAll('#layout-navbar .dropdown-menu'));
  }

  // Navbar scroll handling
  const handleNavbarScroll = () => {
    if (window.scrollY > 10) {
      nav?.classList.add('navbar-active');
    } else {
      nav?.classList.remove('navbar-active');
    }
  };

  window.addEventListener('scroll', handleNavbarScroll);
  window.addEventListener('load', handleNavbarScroll);

  // Close the mobile menu
  function closeMenu() {
    if (menu) {
      menu.classList.remove('show');
    }
  }

  document.addEventListener('click', function (event) {
    if (menu && !menu.contains(event.target)) {
      closeMenu();
    }
  });

  navItemLink.forEach(link => {
    link.addEventListener('click', event => {
      if (!link.classList.contains('dropdown-toggle')) {
        closeMenu();
      } else {
        event.preventDefault();
      }
    });
  });

  // Mega dropdown
  const megaDropdown = document.querySelectorAll('.nav-link.mega-dropdown');
  if (megaDropdown) {
    megaDropdown.forEach(e => {
      new MegaDropdown(e);
    });
  }

  // Style Switcher (Light/Dark/System Mode)
  const styleSwitcher = document.querySelector('.dropdown-style-switcher');
  const activeStyle = document.documentElement.getAttribute('data-style');
  const storedStyle = localStorage.getItem('templateCustomizer-' + templateName + '--Style') ||
                      (window.templateCustomizer?.settings?.defaultStyle ?? 'light');

  if (window.templateCustomizer && styleSwitcher) {
    const styleSwitcherItems = [].slice.call(styleSwitcher.querySelectorAll('.dropdown-item'));
    styleSwitcherItems.forEach(function (item) {
      item.classList.remove('active');
      item.addEventListener('click', function () {
        const currentStyle = this.getAttribute('data-theme');
        window.templateCustomizer.setStyle(currentStyle || 'light');
      });

      setTimeout(() => {
        if (item.getAttribute('data-theme') === activeStyle) {
          item.classList.add('active');
        }
      }, 1000);
    });

    const styleSwitcherIcon = styleSwitcher.querySelector('i');
    if (storedStyle === 'light') {
      styleSwitcherIcon.classList.add('ti-sun');
    } else if (storedStyle === 'dark') {
      styleSwitcherIcon.classList.add('ti-moon-stars');
    } else {
      styleSwitcherIcon.classList.add('ti-device-desktop-analytics');
    }
  }

  function switchImage(style) {
    if (style === 'system') {
      style = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    const switchImagesList = [].slice.call(document.querySelectorAll('[data-app-' + style + '-img]'));
    switchImagesList.forEach(imageEl => {
      const setImage = imageEl.getAttribute('data-app-' + style + '-img');
      imageEl.src = assetsPath + 'img/' + setImage;
    });
  }

  switchImage(storedStyle);
});
