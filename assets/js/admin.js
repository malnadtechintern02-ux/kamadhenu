/**
 * Admin Panel JavaScript
 * Kamadenu Goushala
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // Sidebar Toggle for Mobile
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });

        // Close when clicking outside on mobile
        document.addEventListener('click', function (e) {
            if (window.innerWidth < 992 && !sidebar.contains(e.target) && !toggleBtn.contains(e.target) && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });
    }

    // Auto slug generator from Title/Name input
    const titleInput = document.querySelector('input[data-slug-source]');
    const slugInput = document.querySelector('input[data-slug-target]');
    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function () {
            if (!slugInput.dataset.manualEdit) {
                slugInput.value = generateSlug(this.value);
            }
        });
        slugInput.addEventListener('input', function () {
            this.dataset.manualEdit = 'true';
        });
    }

    function generateSlug(text) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-');
    }

    // Image preview before upload
    const imageInputs = document.querySelectorAll('input[type="file"][data-preview]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function () {
            const previewId = this.getAttribute('data-preview');
            const previewEl = document.getElementById(previewId);
            if (previewEl && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewEl.src = e.target.result;
                    previewEl.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Delete Confirmation
    const deleteBtns = document.querySelectorAll('[data-confirm-delete]');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const msg = this.getAttribute('data-confirm-delete') || 'Are you sure you want to delete this record? This action cannot be undone.';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });
});
