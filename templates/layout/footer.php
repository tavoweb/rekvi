<?php // templates/layout/footer.php ?>
            </div> <?php // Closing .content-inner from header.php ?>
        </div> <?php // Closing .main-content from header.php ?>
    </div> <?php // Closing .page-wrapper from header.php ?>

    <footer class="site-footer">
        <p><?php echo trans('copyright_text', ['year' => date('Y')]); ?></p>
    </footer>

    <script src="/js/script.js"></script> <?php // Root-relative path for JS ?>
</body>
</html>