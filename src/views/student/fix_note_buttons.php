<?php
// This file contains a fix for the note creation buttons
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Fix note buttons script loaded');
    
    // Direct fix for note buttons
    var addNoteBtn = document.getElementById('addNoteBtn');
    var emptyAddNoteBtn = document.getElementById('emptyAddNoteBtn');
    var addNoteModal = document.getElementById('addNoteModal');
    
    if (addNoteBtn && addNoteModal) {
        console.log('Adding direct click handler to addNoteBtn');
        addNoteBtn.onclick = function() {
            console.log('addNoteBtn clicked directly');
            addNoteModal.style.display = 'block';
            setTimeout(function() {
                document.getElementById('noteTitle').focus();
            }, 300);
        };
    }
    
    if (emptyAddNoteBtn && addNoteModal) {
        console.log('Adding direct click handler to emptyAddNoteBtn');
        emptyAddNoteBtn.onclick = function() {
            console.log('emptyAddNoteBtn clicked directly');
            addNoteModal.style.display = 'block';
            setTimeout(function() {
                document.getElementById('noteTitle').focus();
            }, 300);
        };
    }
});
</script>