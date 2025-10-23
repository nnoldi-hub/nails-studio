    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Admin JS -->
    <script src="../assets/js/main.js"></script>
    
    <script>
        // Admin specific JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize data tables if available
            if (typeof DataTable !== 'undefined') {
                const tables = document.querySelectorAll('table.data-table');
                tables.forEach(table => {
                    new DataTable(table, {
                        responsive: true,
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ro.json'
                        }
                    });
                });
            }
            
            // Confirm delete actions
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Ești sigur că vrei să ștergi acest element?')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    }
                }, 5000);
            });
        });
    </script>
</body>
</html>
