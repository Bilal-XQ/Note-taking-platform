// Fix for modal close buttons
document.addEventListener('DOMContentLoaded', function() {
    console.log('Modal fix script loaded');

    // Get modal elements
    const addNoteModal = document.getElementById('addNoteModal');
    const closeNoteModal = document.getElementById('closeNoteModal');
    const cancelNoteBtn = document.getElementById('cancelNoteBtn');

    // Add event listeners for close buttons
    if (closeNoteModal && addNoteModal) {
        closeNoteModal.addEventListener('click', function() {
            console.log('Close button clicked');
            addNoteModal.style.display = 'none';
        });
    }

    if (cancelNoteBtn && addNoteModal) {
        cancelNoteBtn.addEventListener('click', function() {
            console.log('Cancel button clicked');
            addNoteModal.style.display = 'none';
        });
    }

    // Also fix the module modal close buttons
    const addModuleModal = document.getElementById('addModuleModal');
    const closeModuleModal = document.getElementById('closeModuleModal');
    const cancelModuleBtn = document.getElementById('cancelModuleBtn');

    if (closeModuleModal && addModuleModal) {
        closeModuleModal.addEventListener('click', function() {
            addModuleModal.style.display = 'none';
        });
    }

    if (cancelModuleBtn && addModuleModal) {
        cancelModuleBtn.addEventListener('click', function() {
            addModuleModal.style.display = 'none';
        });
    }

    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === addModuleModal) {
            console.log('Clicked outside module modal');
            addModuleModal.style.display = 'none';
        }
        if (event.target === addNoteModal) {
            console.log('Clicked outside note modal');
            addNoteModal.style.display = 'none';
        }
    });
});
