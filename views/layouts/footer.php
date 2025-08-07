</div>
<!-- / Layout page -->
</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="<?= asset('assets/vendor/libs/jquery/jquery.js') ?>"></script>
<script src="<?= asset('assets/vendor/libs/popper/popper.js') ?>"></script>
<script src="<?= asset('assets/vendor/js/bootstrap.js') ?>"></script>
<script src="<?= asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') ?>"></script>

<script src="<?= asset('assets/vendor/js/menu.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="<?= asset('assets/vendor/libs/apex-charts/apexcharts.js') ?>"></script>

<!-- Main JS -->
<script src="<?= asset('assets/js/main.js') ?>"></script>

<!-- Page JS -->
<script src="<?= asset('assets/js/dashboards-analytics.js') ?>"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

<script>
    // Function to display a Bootstrap alert
    function showAlert(message, alertType, id = "alert-container") {
        var alertContainer = $('#' + id);
        var alert = $('<div class="alert alert-' + alertType + ' alert-dismissible fade show" role="alert">' + message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        alertContainer.html(alert);
    }
</script>
</body>

</html>