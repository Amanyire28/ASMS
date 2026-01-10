/**
 * SPA Router for Laravel Integration
 * Handles client-side routing without page refreshes
 */

class SPARouter {
    constructor(options = {}) {
        this.routes = new Map();
        this.middlewares = [];
        this.currentRoute = null;
        this.contentTarget = options.contentTarget || '#page-content';
        this.mobileContentTarget = options.mobileContentTarget || '#page-content-mobile';
        this.baseUrl = options.baseUrl || '';
        this.loadingIndicator = options.loadingIndicator || '#loading-indicator';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // Cache for loaded content
        this.cache = new Map();
        this.cacheEnabled = options.cache !== false;
        this.cacheExpiry = options.cacheExpiry || 300000; // 5 minutes default

        this.init();
    }

    init() {
        // Intercept all link clicks
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (!link) return;

            const href = link.getAttribute('href');

            // Skip external links, anchors, and special protocols
            if (!href ||
                href.startsWith('http://') ||
                href.startsWith('https://') ||
                href.startsWith('#') ||
                href.startsWith('mailto:') ||
                href.startsWith('tel:') ||
                link.hasAttribute('download') ||
                link.getAttribute('target') === '_blank' ||
                link.closest('form') ||
                link.hasAttribute('data-spa') && link.getAttribute('data-spa') === 'false') {
                return;
            }

            e.preventDefault();
            e.stopPropagation();
            console.log('🔗 SPA Router intercepted:', href);
            this.navigate(href);
        }, true);

        // Handle browser back/forward buttons
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.path) {
                this.navigate(e.state.path, { pushState: false });
            } else {
                this.navigate(window.location.pathname, { pushState: false });
            }
        });

        // Handle form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.hasAttribute('data-spa-form')) {
                e.preventDefault();
                this.submitForm(form);
            }
        });

        // Listen for Alpine initialization to reinitialize on SPA navigation
        document.addEventListener('alpine:initialized', () => {
            console.log('🎯 Alpine.js initialized, setting up SPA integration');
            this.setupAlpineSPAIntegration();
        });

        // If Alpine is already loaded
        if (window.Alpine) {
            this.setupAlpineSPAIntegration();
        }

        console.log('✅ SPA Router initialized');
    }

    setupAlpineSPAIntegration() {
        // Store original Alpine methods
        if (window.Alpine) {
            if (!window.Alpine.originalDiscover) {
                window.Alpine.originalDiscover = window.Alpine.discover;
            }
            if (!window.Alpine.originalInitTree) {
                window.Alpine.originalInitTree = window.Alpine.initTree;
            }
        }
    }

    /**
     * Register a middleware function
     */
    use(middleware) {
        this.middlewares.push(middleware);
        return this;
    }

    /**
     * Navigate to a new route
     */
    async navigate(path, options = {}) {
        const {
            pushState = true,
            forceReload = false,
            method = 'GET',
            data = null
        } = options;

        try {
            // Run middlewares
            for (const middleware of this.middlewares) {
                const result = await middleware(path, options);
                if (result === false) return;
            }

            // Show loading indicator
            this.showLoading();

            // Clear any form validation errors
            this.clearFormErrors();

            // Check cache
            const cacheKey = `${method}:${path}`;
            if (this.cacheEnabled && !forceReload && this.cache.has(cacheKey)) {
                const cached = this.cache.get(cacheKey);
                if (Date.now() - cached.timestamp < this.cacheExpiry) {
                    console.log('📦 Loading from cache:', path);
                    await this.render(cached.content, path);
                    this.hideLoading();
                    if (pushState) {
                        this.updateHistory(path);
                    }
                    // Dispatch navigation event AFTER Alpine initialization
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('spa:navigated', {
                            detail: { path }
                        }));
                    }, 100);
                    return;
                }
            }

            // Fetch content from Laravel backend
            const response = await this.fetchContent(path, method, data);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const html = await response.text();

            // Cache the response
            if (this.cacheEnabled && method === 'GET') {
                this.cache.set(cacheKey, {
                    content: html,
                    timestamp: Date.now()
                });
            }

            // Render content
            await this.render(html, path);

            // Update browser history
            if (pushState) {
                this.updateHistory(path);
            }

            // Update page title if provided
            const title = response.headers.get('X-Page-Title');
            if (title) {
                document.title = title;
            }

            // Dispatch navigation event AFTER rendering and Alpine initialization
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('spa:navigated', {
                    detail: { path, response }
                }));
            }, 100);

        } catch (error) {

            this.handleError(error, path);
        } finally {
            this.hideLoading();
        }
    }

    /**
     * Fetch content from Laravel backend
     */
    async fetchContent(path, method = 'GET', data = null) {
        const url = this.baseUrl + path;
        const options = {
            method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'text/html, application/json',
                'X-SPA-Request': 'true'
            },
            credentials: 'same-origin'
        };

        if (data) {
            if (data instanceof FormData) {
                options.body = data;
            } else {
                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(data);
            }
        }

        return fetch(url, options);
    }

    /**
     * Render HTML content into the page
     */
    async render(html, path) {
        // Check if response is JSON
        try {
            const data = JSON.parse(html);

            if (data.html) {
                // JSON contains HTML - render it
                html = data.html;
            } else if (data.table) {
                // JSON contains table data (for AJAX search)
                html = data.table;
            } else if (data.redirect) {
                // JSON contains redirect URL
                await this.navigate(data.redirect);
                return;
            } else {
                // Other JSON response - show error or handle differently
                console.warn('Unexpected JSON response:', data);
                this.showAlert('Received unexpected response format', 'danger');
                return;
            }
        } catch (e) {
            // Not JSON, proceed with HTML rendering
        }

        let content = html;

        // If response is a full HTML document, extract the content
        if (html.includes('<!DOCTYPE html>')) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const pageContent = doc.getElementById('page-content');

            if (pageContent) {
                content = pageContent.innerHTML;
            } else {
                throw new Error('Could not find #page-content in response');
            }
        }

        // Clean up old Alpine states
        this.cleanupAlpineStates();

        // Update both desktop and mobile content areas
        const desktopTarget = document.querySelector(this.contentTarget);
        const mobileTarget = document.querySelector(this.mobileContentTarget);

        if (desktopTarget) {
            desktopTarget.innerHTML = content;
            console.log('📝 Desktop content updated for:', path);

            // CRITICAL: Initialize Alpine.js on new content
            this.initializeAlpineContent(desktopTarget, path);
        }

        if (mobileTarget) {
            mobileTarget.innerHTML = content;
            console.log('📝 Mobile content updated for:', path);

            // Initialize Alpine on mobile content too
            this.initializeAlpineContent(mobileTarget, path);
        }

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Dispatch render event
        window.dispatchEvent(new CustomEvent('spa:rendered', {
            detail: { path, content }
        }));
    }

    /**
     * Initialize Alpine.js on new content
     */
    initializeAlpineContent(element, path) {
        if (!window.Alpine) {
            console.warn('Alpine.js not loaded');
            return;
        }

        // Use setTimeout to ensure DOM is fully updated
        setTimeout(() => {
            try {
                console.log('🎯 Initializing Alpine.js on:', path);

                // Method 1: Use Alpine.discover if available (Alpine v3)
                if (typeof Alpine.discover === 'function') {
                    console.log('🔍 Using Alpine.discover()');
                    Alpine.discover();
                }
                // Method 2: Use Alpine.initTree (Alpine v3)
                else if (typeof Alpine.initTree === 'function') {
                    console.log('🌲 Using Alpine.initTree()');
                    Alpine.initTree(element);
                }
                // Method 3: Manually walk through Alpine components (fallback)
                else {
                    console.log('🔧 Manually initializing Alpine components');
                    this.initializeAlpineManually(element);
                }

                // Force Alpine to scan for new components
                this.forceAlpineDiscovery();

            } catch (error) {
                console.error('❌ Alpine initialization error:', error);
            }
        }, 20);
    }

    /**
     * Force Alpine to discover new components
     */
    forceAlpineDiscovery() {
        if (!window.Alpine) return;

        // Try multiple methods to ensure Alpine picks up new components
        setTimeout(() => {
            // Method 1: Dispatch alpine:init event
            document.dispatchEvent(new CustomEvent('alpine:init'));

            // Method 2: Trigger mutation observer if available
            if (Alpine.mutationObserver) {
                Alpine.mutationObserver.takeRecords();
            }

            // Method 3: Manually trigger discovery on all x-data elements
            document.querySelectorAll('[x-data]').forEach(el => {
                if (!el.__x) {
                    try {
                        if (Alpine.initTree) Alpine.initTree(el);
                    } catch (e) {
                        // Ignore errors
                    }
                }
            });
        }, 50);
    }

    /**
     * Manually initialize Alpine components
     */
    initializeAlpineManually(element) {
        // Find all elements with x-data attribute
        const alpineElements = element.querySelectorAll('[x-data]');

        alpineElements.forEach(el => {
            try {
                // Check if element already has Alpine scope
                if (!el.__x) {
                    // Parse the x-data expression
                    const dataExpression = el.getAttribute('x-data');
                    if (dataExpression) {
                        // Create a new Alpine component
                        const component = Alpine.createRawComponent(() => {
                            return Function(`return (${dataExpression})`)();
                        });

                        // Initialize Alpine on the element
                        Alpine.addScopeToNode(el, component);
                        Alpine.initTree(el);
                    }
                }
            } catch (e) {
                console.warn('Could not initialize Alpine on element:', e);
            }
        });

        // Also look for elements with x-init
        const initElements = element.querySelectorAll('[x-init]');
        initElements.forEach(el => {
            try {
                if (!el.__x) {
                    Alpine.initTree(el);
                }
            } catch (e) {
                console.warn('Could not run x-init on element:', e);
            }
        });
    }

    /**
     * Clean up Alpine states from old content
     */
    cleanupAlpineStates() {
        if (!window.Alpine) return;

        // Find and destroy any Alpine components in the content areas
        const contentAreas = [
            document.querySelector(this.contentTarget),
            document.querySelector(this.mobileContentTarget)
        ];

        contentAreas.forEach(area => {
            if (!area) return;

            // Find Alpine components and destroy them
            const alpineElements = area.querySelectorAll('[x-data]');
            alpineElements.forEach(el => {
                if (el.__x) {
                    try {
                        // Call destroy method if available
                        if (el.__x.destroy) {
                            el.__x.destroy();
                        }
                        if (el.__x.tearDown) {
                            el.__x.tearDown();
                        }
                        // Remove Alpine reference
                        delete el.__x;
                    } catch (e) {
                        // Ignore cleanup errors
                    }
                }
            });
        });
    }

    /**
     * Submit form via AJAX
     */
    async submitForm(form) {
        try {
            this.showLoading();

            const formData = new FormData(form);
            const method = form.getAttribute('method') || 'POST';
            const action = form.getAttribute('action') || window.location.pathname;

            const response = await this.fetchContent(action, method, formData);

            if (!response.ok) {
                if (response.status === 422) {
                    const errors = await response.json();
                    this.displayValidationErrors(form, errors);
                } else {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return;
            }

            const html = await response.text();

            // Check if response is JSON
            try {
                const json = JSON.parse(html);
                if (json.redirect) {
                    await this.navigate(json.redirect);
                } else if (json.message) {
                    this.showAlert(json.message, json.type || 'success');
                    // Reinitialize Alpine after form submission
                    setTimeout(() => this.forceAlpineDiscovery(), 100);
                }
                return;
            } catch (e) {
                await this.render(html, action);
            }

            const successMessage = response.headers.get('X-Success-Message');
            if (successMessage) {
                this.showAlert(successMessage, 'success');
            }

            const redirectUrl = response.headers.get('X-Redirect');
            if (redirectUrl) {
                await this.navigate(redirectUrl);
            }

        } catch (error) {
            console.error('Form submission error:', error);
            this.showAlert('An error occurred. Please try again.', 'danger');
        } finally {
            this.hideLoading();
        }
    }

    /**
     * Clear form validation errors
     */
    clearFormErrors() {
        document.querySelectorAll('.text-red-500, .text-red-600').forEach(el => el.remove());
        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
        });
    }

    /**
     * Display validation errors on form
     */
    displayValidationErrors(form, errors) {
        this.clearFormErrors();

        Object.keys(errors.errors || errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('border-red-500');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-red-500 text-sm mt-1';
                errorDiv.textContent = errors.errors?.[field]?.[0] || errors[field];
                input.parentElement.appendChild(errorDiv);
            }
        });

        this.showAlert('Please fix the errors and try again.', 'danger');
    }

    /**
     * Update browser history
     */
    updateHistory(path) {
        const state = { path, timestamp: Date.now() };

        if (window.location.pathname === path) {
            window.history.replaceState(state, '', path);
        } else {
            window.history.pushState(state, '', path);
        }

        this.currentRoute = path;
    }

    /**
     * Show loading indicator
     */
    showLoading() {
        const indicators = [
            document.querySelector(this.loadingIndicator),
            document.querySelector('#loading-indicator-mobile')
        ];

        indicators.forEach(indicator => {
            if (indicator) {
                indicator.style.opacity = '1';
                indicator.style.pointerEvents = 'auto';
            }
        });
    }

    /**
     * Hide loading indicator
     */
    hideLoading() {
        const indicators = [
            document.querySelector(this.loadingIndicator),
            document.querySelector('#loading-indicator-mobile')
        ];

        indicators.forEach(indicator => {
            if (indicator) {
                indicator.style.opacity = '0';
                indicator.style.pointerEvents = 'none';
            }
        });
    }

    /**
     * Show alert message
     */
    showAlert(message, type = 'success') {
        const icons = {
            success: 'fa-check-circle',
            danger: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle'
        };

        const colors = {
            success: 'bg-green-50 border-green-500 text-green-700 dark:bg-green-900/20 dark:border-green-400 dark:text-green-300',
            danger: 'bg-red-50 border-red-500 text-red-700 dark:bg-red-900/20 dark:border-red-400 dark:text-red-300',
            warning: 'bg-yellow-50 border-yellow-500 text-yellow-700 dark:bg-yellow-900/20 dark:border-yellow-400 dark:text-yellow-300'
        };

        const alertDiv = document.createElement('div');
        alertDiv.className = `mb-4 border-l-4 ${colors[type]} p-4 rounded-lg flash-message`;
        alertDiv.setAttribute('x-data', '{ show: true }');
        alertDiv.setAttribute('x-show', 'show');
        alertDiv.setAttribute('x-init', 'setTimeout(() => show = false, 5000)');
        alertDiv.setAttribute('x-transition', '');
        alertDiv.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas ${icons[type]} flex-shrink-0"></i>
                    <p class="ml-3">${this.escapeHtml(message)}</p>
                </div>
                <button @click="show = false" class="hover:opacity-70">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        const containers = [
            document.getElementById('flash-messages'),
            document.getElementById('flash-messages-mobile')
        ];

        containers.forEach(container => {
            if (container) {
                container.appendChild(alertDiv);

                // Initialize Alpine on the new alert
                if (window.Alpine) {
                    setTimeout(() => {
                        if (Alpine.initTree) Alpine.initTree(alertDiv);
                    }, 10);
                }
            }
        });
    }

    /**
     * Handle navigation errors
     */
    handleError(error, path) {
        console.error('Router error:', error);
        this.showAlert(`Failed to load page: ${error.message}`, 'danger');

        // Fallback to full page reload on critical errors
        if (error.message.includes('failed') || error.message.includes('network')) {
            setTimeout(() => {
                window.location.href = path;
            }, 2000);
        }
    }

    /**
     * Clear cache
     */
    clearCache(pattern = null) {
        if (pattern) {
            for (const key of this.cache.keys()) {
                if (key.includes(pattern)) {
                    this.cache.delete(key);
                }
            }
        } else {
            this.cache.clear();
        }
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Preload a route
     */
    async preload(path) {
        const cacheKey = `GET:${path}`;
        if (this.cache.has(cacheKey)) return;

        try {
            const response = await this.fetchContent(path);
            const html = await response.text();

            this.cache.set(cacheKey, {
                content: html,
                timestamp: Date.now()
            });

            console.log('📦 Preloaded:', path);
        } catch (error) {
            console.error('Preload error:', error);
        }
    }

    /**
     * Refresh current route
     */
    refresh() {
        return this.navigate(window.location.pathname, { forceReload: true });
    }
}

// Initialize router when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.router = new SPARouter({
        contentTarget: '#page-content',
        mobileContentTarget: '#page-content-mobile',
        loadingIndicator: '#loading-indicator',
        cache: true,
        cacheExpiry: 300000
    });

    // Add default middleware
    window.router.use(async (path, options) => {
        console.log('🛣️ Navigating to:', path);
        return true;
    });

    // Preload common routes
    setTimeout(() => {
        const commonRoutes = ['/dashboard', '/profile', '/settings'];
        commonRoutes.forEach(route => {
            window.router.preload(route);
        });
    }, 1000);

    console.log('✅ SPA Router ready. Access via window.router');
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = SPARouter;
}
