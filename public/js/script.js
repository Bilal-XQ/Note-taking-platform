/**
 * NoteMaster - Student Note-Taking Platform
 * Main JavaScript file for UI interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all interactive elements
    initModals();
    initFloatingActionButton();
    initEditButtons();
});

/**
 * Modal handling
 */
function initModals() {
    // Open modal triggers
    document.querySelectorAll('[data-open-modal]').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = button.getAttribute('data-open-modal');
            const modal = document.getElementById(modalId);
            
            // Special handling for dynamically setting attributes
            if (modalId === 'addNoteModal' && button.hasAttribute('data-module-id')) {
                const moduleId = button.getAttribute('data-module-id');
                document.getElementById('noteModuleId').value = moduleId;
            }
            
            if (modal) {
                openModal(modal);
            }
        });
    });
    
    // Close modal triggers
    document.querySelectorAll('[data-close-modal]').forEach(element => {
        element.addEventListener('click', () => {
            const modal = element.closest('.modal');
            if (modal) {
                closeModal(modal);
            }
        });
    });
    
    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal[aria-hidden="false"]');
            if (openModal) {
                closeModal(openModal);
            }
        }
    });
}

function openModal(modal) {
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeModal(modal) {
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = ''; // Restore scrolling
}

/**
 * Floating Action Button for Module Creation
 */
function initFloatingActionButton() {
    const fab = document.getElementById('addModuleBtn');
    if (fab) {
        fab.addEventListener('click', () => {
            const moduleModal = document.getElementById('addModuleModal');
            if (moduleModal) {
                openModal(moduleModal);
            }
        });
    }
}

/**
 * Student Edit Button in Admin Dashboard
 */
function initEditButtons() {
    document.querySelectorAll('[data-open-modal="editStudentModal"]').forEach(button => {
        button.addEventListener('click', () => {
            // Populate the edit form with student data from data attributes
            const studentId = button.getAttribute('data-student-id');
            const studentName = button.getAttribute('data-student-name');
            const studentUsername = button.getAttribute('data-student-username');
            
            // Set values in the edit form
            const form = document.querySelector('#editStudentModal form');
            if (form) {
                document.getElementById('editStudentId').value = studentId;
                document.getElementById('editFullName').value = studentName;
                document.getElementById('editUsername').value = studentUsername;
                document.getElementById('editPassword').value = ''; // Clear password field
            }
            
            // Open the modal
            openModal(document.getElementById('editStudentModal'));
        });
    });
}

/**
 * Legacy Login Modal Toggle Logic
 * Note: This keeps backward compatibility with the original login modal
 */
const loginModal = document.getElementById('loginModal');
const loginModalClose = document.getElementById('loginModalClose');
const loginModalOverlay = document.getElementById('loginModalOverlay');
const loginForm = document.getElementById('loginForm');
const headerLoginBtn = document.querySelector('.header__btn--login');

// Find header and hero buttons (updated classes)
if (headerLoginBtn) headerLoginBtn.addEventListener('click', e => { 
    e.preventDefault(); 
    if (loginModal) loginModal.style.display = 'flex';
});

// Legacy cookie functions
function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days*24*60*60*1000));
    document.cookie = `${name}=${encodeURIComponent(value)};expires=${d.toUTCString()};path=/`;
}

function getCookie(name) {
    const v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
    return v ? decodeURIComponent(v[2]) : null;
}

// Remember me cookie handling
if (loginForm) {
    // Prefill username from cookie
    const remembered = getCookie('remembered_user');
    if (remembered && loginForm) {
        loginForm.username.value = remembered;
        loginForm["remember"].checked = true;
    }
    
    // Set cookie on form submit
    loginForm.addEventListener('submit', function() {
        if (loginForm["remember"].checked) {
            setCookie('remembered_user', loginForm.username.value, 30);
        } else {
            setCookie('remembered_user', '', -1);
        }
    });
}

// Legacy modal close handlers
if (loginModalClose) loginModalClose.addEventListener('click', () => {
    if (loginModal) loginModal.style.display = 'none';
});

if (loginModalOverlay) loginModalOverlay.addEventListener('click', () => {
    if (loginModal) loginModal.style.display = 'none';
}); 