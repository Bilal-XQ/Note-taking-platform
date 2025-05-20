// Fix for note buttons not working
console.log('Note buttons fix script loaded');

// Function to be executed when the DOM is fully loaded
function fixNoteButtons() {
    console.log('Fixing note buttons');
    
    // Get the buttons and modal
    var addNoteBtn = document.getElementById('addNoteBtn');
    var emptyAddNoteBtn = document.getElementById('emptyAddNoteBtn');
    var addNoteModal = document.getElementById('addNoteModal');
    
    console.log('Elements found:', {
        addNoteBtn: !!addNoteBtn,
        emptyAddNoteBtn: !!emptyAddNoteBtn,
        addNoteModal: !!addNoteModal
    });
    
    // Function to open the modal
    function openNoteModal() {
        console.log('Opening note modal');
        if (addNoteModal) {
            addNoteModal.style.display = 'block';
            setTimeout(function() {
                var noteTitle = document.getElementById('noteTitle');
                if (noteTitle) noteTitle.focus();
            }, 300);
        }
    }
    
    // Attach click handlers directly to the buttons
    if (addNoteBtn) {
        console.log('Attaching click handler to addNoteBtn');
        addNoteBtn.onclick = openNoteModal;
    }
    
    if (emptyAddNoteBtn) {
        console.log('Attaching click handler to emptyAddNoteBtn');
        emptyAddNoteBtn.onclick = openNoteModal;
    }
}

// Run the function when the DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', fixNoteButtons);
} else {
    // If DOMContentLoaded has already fired, run the function immediately
    fixNoteButtons();
}

// Also run it after a short delay to ensure it runs after any other scripts
setTimeout(fixNoteButtons, 500);