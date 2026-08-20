/**
 * Kamadenu Goushala – Main JavaScript
 * Vanilla JS for interactivity, animations, and form handling.
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // ── Navbar Scroll Effect ──────────────────────────────────
    const navbar = document.getElementById('mainNavbar');
    if (navbar) {
        const handleScroll = () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll(); // Initial check
    }

    // ── Back to Top Button ────────────────────────────────────
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 400) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        }, { passive: true });

        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ── Scroll Animations (Intersection Observer) ─────────────
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    if (animatedElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        });

        animatedElements.forEach(el => observer.observe(el));
    }

    // ── Counter Animation ─────────────────────────────────────
    const counters = document.querySelectorAll('[data-counter]');
    if (counters.length > 0) {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => counterObserver.observe(counter));
    }

    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-counter'));
        const suffix = element.getAttribute('data-suffix') || '';
        const prefix = element.getAttribute('data-prefix') || '';
        const duration = 2000;
        const steps = 60;
        const increment = target / steps;
        let current = 0;
        let step = 0;

        const timer = setInterval(() => {
            step++;
            current = Math.min(Math.round(increment * step), target);
            element.textContent = prefix + current.toLocaleString('en-IN') + suffix;
            if (step >= steps) {
                clearInterval(timer);
                element.textContent = prefix + target.toLocaleString('en-IN') + suffix;
            }
        }, duration / steps);
    }

    // ── Amount Selection ──────────────────────────────────────
    const amountBtns = document.querySelectorAll('.amount-btn');
    const amountInput = document.getElementById('donationAmount') || document.getElementById('customAmount');
    
    amountBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            amountBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const amount = this.getAttribute('data-amount');
            if (amountInput) {
                amountInput.value = amount;
                amountInput.dispatchEvent(new Event('input'));
            }
        });
    });

    // ── Adoption Duration Calculator ──────────────────────────
    const durationSelect = document.getElementById('adoptionDuration');
    const monthlyAmountEl = document.getElementById('monthlyAmount');
    const totalAmountEl = document.getElementById('totalAmount');
    const totalAmountInput = document.getElementById('totalAmountInput');

    if (durationSelect && monthlyAmountEl) {
        durationSelect.addEventListener('change', calculateAdoptionTotal);
        calculateAdoptionTotal(); // Initial
    }

    function calculateAdoptionTotal() {
        if (!durationSelect || !monthlyAmountEl) return;
        
        const monthly = parseFloat(monthlyAmountEl.getAttribute('data-amount') || monthlyAmountEl.textContent.replace(/[^0-9.]/g, ''));
        const months = parseInt(durationSelect.value) || 1;
        const total = monthly * months;

        if (totalAmountEl) {
            totalAmountEl.textContent = '₹' + total.toLocaleString('en-IN');
        }
        if (totalAmountInput) {
            totalAmountInput.value = total;
        }
    }

    // ── Form Validation ───────────────────────────────────────
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // ── Phone Number Formatting ───────────────────────────────
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });
    });

    // ── Gallery Lightbox ──────────────────────────────────────
    const galleryItems = document.querySelectorAll('[data-gallery]');
    if (galleryItems.length > 0) {
        // Create modal if it doesn't exist
        if (!document.getElementById('galleryModal')) {
            const modalHTML = `
                <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content bg-dark border-0">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title text-white" id="galleryModalLabel"></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center p-2">
                                <img src="" alt="" class="img-fluid rounded" id="galleryModalImage" style="max-height: 75vh; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        const galleryModal = new bootstrap.Modal(document.getElementById('galleryModal'));
        const galleryModalImage = document.getElementById('galleryModalImage');
        const galleryModalLabel = document.getElementById('galleryModalLabel');

        galleryItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const imgSrc = this.getAttribute('data-gallery') || this.querySelector('img')?.src;
                const caption = this.getAttribute('data-caption') || this.querySelector('img')?.alt || '';
                
                if (imgSrc && galleryModalImage) {
                    galleryModalImage.src = imgSrc;
                    galleryModalImage.alt = caption;
                    galleryModalLabel.textContent = caption;
                    galleryModal.show();
                }
            });
        });
    }

    // ── Gallery Filter ────────────────────────────────────────
    const filterBtns = document.querySelectorAll('.filter-btn');
    const filterItems = document.querySelectorAll('[data-filter-category]');
    
    if (filterBtns.length > 0 && filterItems.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const category = this.getAttribute('data-filter');
                
                filterItems.forEach(item => {
                    if (category === 'all' || item.getAttribute('data-filter-category') === category) {
                        item.style.display = '';
                        setTimeout(() => item.style.opacity = '1', 10);
                    } else {
                        item.style.opacity = '0';
                        setTimeout(() => item.style.display = 'none', 300);
                    }
                });
            });
        });
    }

    // ── Toast Notifications ───────────────────────────────────
    window.showToast = function (message, type = 'success') {
        const iconMap = {
            success: 'bi-check-circle-fill',
            error: 'bi-exclamation-triangle-fill',
            warning: 'bi-exclamation-circle-fill',
            info: 'bi-info-circle-fill'
        };

        const bgMap = {
            success: '#2E7D32',
            error: '#C62828',
            warning: '#F57F17',
            info: '#0277BD'
        };

        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        
        const toastEl = document.createElement('div');
        toastEl.className = 'toast align-items-center border-0 show';
        toastEl.setAttribute('role', 'alert');
        toastEl.style.backgroundColor = bgMap[type] || bgMap.info;
        toastEl.style.color = 'white';
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${iconMap[type] || iconMap.info} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        toastContainer.appendChild(toastEl);

        setTimeout(() => {
            toastEl.classList.add('hiding');
            setTimeout(() => toastEl.remove(), 300);
        }, 4000);
    };

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1080';
        document.body.appendChild(container);
        return container;
    }

    // ── AJAX Form Submission ──────────────────────────────────
    window.submitFormAjax = function (form, callback) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : '';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (callback) callback(data);
            else if (data.success) {
                showToast(data.message || 'Success!', 'success');
                if (data.redirect) window.location.href = data.redirect;
            } else {
                showToast(data.message || 'Something went wrong.', 'error');
            }
        })
        .catch(error => {
            console.error('Form submission error:', error);
            showToast('Network error. Please try again.', 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    };

    // ── Confirm Delete ────────────────────────────────────────
    document.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('[data-confirm]');
        if (deleteBtn) {
            e.preventDefault();
            const message = deleteBtn.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
            
            if (confirm(message)) {
                const href = deleteBtn.getAttribute('href');
                if (href) window.location.href = href;
                
                const form = deleteBtn.closest('form');
                if (form) form.submit();
            }
        }
    });

    // ── Lazy Loading Images ───────────────────────────────────
    const lazyImages = document.querySelectorAll('img[data-src]');
    if (lazyImages.length > 0) {
        const imgObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.getAttribute('data-src');
                    img.removeAttribute('data-src');
                    img.classList.add('loaded');
                    imgObserver.unobserve(img);
                }
            });
        }, { rootMargin: '100px' });

        lazyImages.forEach(img => imgObserver.observe(img));
    }

    // ── Search Filter (instant search on listing pages) ──────
    const searchInput = document.getElementById('searchInput');
    const searchItems = document.querySelectorAll('[data-searchable]');
    
    if (searchInput && searchItems.length > 0) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            
            searchItems.forEach(item => {
                const text = item.getAttribute('data-searchable').toLowerCase();
                if (query === '' || text.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // ── Auto-dismiss alerts after 5 seconds ───────────────────
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });

    // ── Close mobile nav on link click ────────────────────────
    const navLinks = document.querySelectorAll('#navbarMain .nav-link:not(.dropdown-toggle)');
    const navCollapse = document.getElementById('navbarMain');
    
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992 && navCollapse) {
                const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
                if (bsCollapse) bsCollapse.hide();
            }
        });
    });
});
