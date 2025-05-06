<!-- End of Page Content -->
</main> <!-- Main Content Area Ends -->

</div> <!-- End of Row -->
</div> <!-- End of Container-Fluid -->

<!-- Footer -->
<footer class="text-center mt-4 mb-4 main-content">
    <p class="text-muted">
        <?php echo t('FOOTER_COPYRIGHT', date("Y")); ?>
    </p>
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Pass translation strings to JavaScript -->
<script>
    // Language strings used in admin JavaScript (e.g., confirm dialog in products.php)
    const adminLangStrings = <?php echo json_encode([
        'deleteConfirmProduct' => t('ADMIN_PRODUCTS_DELETE_CONFIRM'),
        // Add more keys as needed for JS use
    ]); ?>;
</script>

<!-- Admin-specific JavaScript (add your own as needed) -->
<!-- Example: <script src="../js/admin_scripts.js"></script> -->

</body>
</html>

<?php
// Optional: Close DB connection if it was opened earlier
if (isset($conn) && $conn instanceof mysqli) {
    mysqli_close($conn);
}
?>
