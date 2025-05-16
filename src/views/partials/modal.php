<?php
/**
 * Modal component
 * Usage: include with different modal IDs and content
 * 
 * Required variables:
 * $modalId - unique ID for the modal
 * $modalTitle - title for the modal header
 * $modalContent - HTML content for the modal body (usually a form)
 */
?>
<div class="modal" id="<?php echo htmlspecialchars($modalId); ?>" aria-hidden="true">
    <div class="modal__overlay" data-close-modal></div>
    <div class="modal__container">
        <header class="modal__header">
            <h3 class="modal__title"><?php echo htmlspecialchars($modalTitle); ?></h3>
            <button class="modal__close" aria-label="Close modal" data-close-modal>&times;</button>
        </header>
        <div class="modal__body">
            <?php echo $modalContent; ?>
        </div>
    </div>
</div> 