// Notes functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Notes script loaded');
    
    // Check if elements exist
    const addNoteBtn = document.getElementById('addNoteBtn');
    const emptyAddNoteBtn = document.getElementById('emptyAddNoteBtn');
    const addNoteModal = document.getElementById('addNoteModal');
    
    console.log('addNoteBtn exists:', !!addNoteBtn);
    console.log('emptyAddNoteBtn exists:', !!emptyAddNoteBtn);
    console.log('addNoteModal exists:', !!addNoteModal);
    
    // Add event listeners if elements exist
    if (addNoteBtn) {
        console.log('Adding click event to addNoteBtn');
        addNoteBtn.addEventListener('click', function() {
            console.log('addNoteBtn clicked');
            if (addNoteModal) {
                addNoteModal.style.display = 'block';
            }
        });
    }
    
    if (emptyAddNoteBtn) {
        console.log('Adding click event to emptyAddNoteBtn');
        emptyAddNoteBtn.addEventListener('click', function() {
            console.log('emptyAddNoteBtn clicked');
            if (addNoteModal) {
                addNoteModal.style.display = 'block';
            }
        });
    }
    
    // Close modal functionality
    const closeNoteModal = document.getElementById('closeNoteModal');
    const cancelNoteBtn = document.getElementById('cancelNoteBtn');
    
    if (closeNoteModal && addNoteModal) {
        closeNoteModal.addEventListener('click', function() {
            addNoteModal.style.display = 'none';
        });
    }
    
    if (cancelNoteBtn && addNoteModal) {
        cancelNoteBtn.addEventListener('click', function() {
            addNoteModal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === addNoteModal) {
            addNoteModal.style.display = 'none';
        }
    });
});