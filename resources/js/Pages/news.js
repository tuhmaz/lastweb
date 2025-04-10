/**
 * News Page Scripts
 */

'use strict';

// DOM Elements
const newsContainer = document.getElementById('news-container');
const categoryLinks = document.querySelectorAll('.category-filter');
const scrollButtons = document.querySelectorAll('.scroll-btn');

// Constants
const HEADER_OFFSET = 80;
const ANIMATION_DELAY = 100;

/**
 * Initialize Intersection Observer for fade animations
 */
const initializeObserver = () => {
    const options = {
        root: null,
        threshold: 0.1,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
                observer.unobserve(entry.target);
            }
        });
    }, options);

    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
};

/**
 * Handle Category Filter
 * @param {Event} e - Click event
 */
const handleCategoryFilter = async (e) => {
    e.preventDefault();
    const link = e.currentTarget;
    const categoryId = link.dataset.categoryId;
    
    if (!newsContainer) return;

    try {
        // Add loading state
        newsContainer.classList.add('loading');
        link.classList.add('disabled');

        const response = await fetch(`${filterUrl}?category_id=${categoryId}`);
        const data = await response.json();

        if (data.success) {
            // Update content with animation
            newsContainer.style.opacity = '0';
            setTimeout(() => {
                newsContainer.innerHTML = data.html;
                newsContainer.style.opacity = '1';
                
                // Reinitialize animations
                const newItems = newsContainer.querySelectorAll('.news-card');
                newItems.forEach((item, index) => {
                    item.style.animationDelay = `${index * 0.1}s`;
                    item.classList.add('animate__animated', 'animate__fadeInUp');
                });

                // Update active state
                categoryLinks.forEach(link => link.classList.remove('active'));
                link.classList.add('active');
            }, 300);
        }
    } catch (error) {
        console.error('Error filtering news:', error);
        // Show error message to user
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger mt-3';
        errorDiv.textContent = 'An error occurred while filtering news. Please try again.';
        newsContainer.prepend(errorDiv);
    } finally {
        // Remove loading state
        newsContainer.classList.remove('loading');
        link.classList.remove('disabled');
    }
};

/**
 * Handle Smooth Scroll
 * @param {Event} e - Click event
 */
const handleSmoothScroll = (e) => {
    e.preventDefault();
    const targetId = e.currentTarget.getAttribute('href');
    const targetElement = document.querySelector(targetId);

    if (targetElement) {
        const elementPosition = targetElement.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - HEADER_OFFSET;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
};

/**
 * Initialize Lazy Loading
 */
const initializeLazyLoading = () => {
    if ('loading' in HTMLImageElement.prototype) {
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            // Add loading animation
            img.style.opacity = '0';
            img.addEventListener('load', () => {
                img.style.transition = 'opacity 0.3s ease-in';
                img.style.opacity = '1';
            });
            img.src = img.src;
        });
    } else {
        // Fallback for browsers that don't support native lazy loading
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lozad.js/1.16.0/lozad.min.js';
        script.async = true;
        script.onload = () => {
            const observer = lozad();
            observer.observe();
        };
        document.body.appendChild(script);
    }
};

/**
 * Initialize all event listeners
 */
const initializeEventListeners = () => {
    categoryLinks.forEach(link => {
        link.addEventListener('click', handleCategoryFilter);
    });

    scrollButtons.forEach(button => {
        button.addEventListener('click', handleSmoothScroll);
    });
};

/**
 * Document ready handler
 */
document.addEventListener('DOMContentLoaded', () => {
    initializeObserver();
    initializeEventListeners();
    initializeLazyLoading();
});
