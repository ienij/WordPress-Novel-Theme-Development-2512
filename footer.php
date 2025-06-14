<footer class="bg-black text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- About Section -->
            <div>
                <h3 class="text-xl font-bold mb-4">
                    <i class="fas fa-book mr-2"></i>
                    <?php bloginfo('name'); ?>
                </h3>
                <p class="text-gray-300 mb-4">
                    Discover and read the latest translated web novels from around the world. Join our community of readers and translators.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i class="fab fa-facebook text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i class="fab fa-discord text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i class="fab fa-reddit text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Support -->
            <div>
                <h4 class="text-lg font-semibold mb-4">
                    <i class="fas fa-life-ring mr-2"></i>
                    Support
                </h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-white"><i class="fas fa-question-circle mr-2"></i>Help Center</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white"><i class="fas fa-envelope mr-2"></i>Contact Us</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white"><i class="fas fa-bug mr-2"></i>Report Issue</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white"><i class="fas fa-shield-alt mr-2"></i>Privacy Policy</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white"><i class="fas fa-file-contract mr-2"></i>Terms of Service</a></li>
                </ul>
            </div>

            <!-- Statistics -->
            <div>
                <h4 class="text-lg font-semibold mb-4">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Site Statistics
                </h4>
                <div class="space-y-2">
                    <?php
                    $novel_count = wp_count_posts('novel')->publish;
                    $chapter_count = wp_count_posts('chapter')->publish;
                    $user_count = count_users()['total_users'];
                    ?>
                    <div class="text-gray-300">
                        <i class="fas fa-book mr-2"></i>
                        <?php echo number_format($novel_count); ?> Novels
                    </div>
                    <div class="text-gray-300">
                        <i class="fas fa-file-alt mr-2"></i>
                        <?php echo number_format($chapter_count); ?> Chapters
                    </div>
                    <div class="text-gray-300">
                        <i class="fas fa-users mr-2"></i>
                        <?php echo number_format($user_count); ?> Members
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8 text-center">
            <p class="text-gray-300 text-sm">
                &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.
            </p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

<script>
// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
});
</script>

</body>
</html>