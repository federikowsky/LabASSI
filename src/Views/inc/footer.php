        </main>
    <footer class="d-flex align-items-center justify-content-between p-3">
        <div class="text-white">
            © 2024 Copyright: miniworld.com
        </div>
        <div class="d-flex justify-content-center">
            <ul class="footer-list px-0 py-2">
                <li class="footer-item">
                    <a href="#" class="footer-link"><i class="fab fa-facebook-f"></i></a>
                </li>
                <li class="footer-item">
                    <a href="#" class="footer-link"><i class="fab fa-twitter"></i></a>
                </li>
                <li class="footer-item">
                    <a href="#" class="footer-link"><i class="fab fa-google-plus-g"></i></a>
                </li>
                <li class="footer-item">
                    <a href="#" class="footer-link"><i class="fab fa-youtube"></i></a>
                </li>
                <li class="footer-item">
                    <a href="#" class="footer-link"><i class="fab fa-linkedin-in"></i></a>
                </li>
            </ul>
        </div>
    </footer>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<?php if (isset($js_files)) : ?>
    <?php foreach ($js_files as $js): ?>
        <script src="<?= $js ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<?php 
    // here because use toastr and need to import the js file first
    // Don't move this line away from here
    flash() 
?>

</html>