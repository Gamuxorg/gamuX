</div>
</div>
<script crossorigin="anonymous" integrity="sha384-+jvb+jCJ37FkNjPyYLI3KJzQeD8pPFXUra3B/QJFqQ3txYrUPIP1eOfxK4h3cKZP" src="https://lib.baomitu.com/vue/2.6.11/vue.js"></script>
<script crossorigin="anonymous" integrity="sha384-WbhdtWslh0AUD1Dhf8OExUvvjZ/VN6o2HHMsYlDXb6uf3IweMH13dGL4V/KgDc7y" src="https://lib.baomitu.com/element-ui/2.13.2/index.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/common.js"></script>
<?php if (is_home() || is_front_page()) { ?>
    <script src="<?php bloginfo('template_url'); ?>/js/index.js"></script>
<?php } ?>
<?php wp_footer(); ?>
</body>
</html>
