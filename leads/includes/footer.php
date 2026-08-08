<?php
/**
 * includes/footer.php
 * Closes the layout structure opened in includes/header.php and loads JS assets.
 */
declare(strict_types=1);
?>
        </div><!-- /.app-content -->
    </div><!-- /.app-main -->
</div><!-- /.app-wrapper -->

<!-- Bootstrap 5 JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?= e(BASE_URL) ?>/assets/js/app.js"></script>

<?php if (!empty($extraScripts)) { echo $extraScripts; } ?>

</body>
</html>
