<?php
// templates/layout/footer.php
?>
        </div> <!-- .content-inner -->
    </div> <!-- .main-content -->
</div> <!-- .page-wrapper -->

<footer>
    <p>&copy; <?php echo date('Y'); ?> Rekvizitų Valdymo Sistema. <?php echo "Šiuo metu yra: " . date("Y-m-d H:i:s"); ?> (EEST)</p>
    <p>Viso įmonių sistemoje: <?php echo $view_data['total_companies'] ?? 0; ?>.</p>
</footer>
<script src="/js/script.js"></script>
</body>
</html>