</div>
</div>
</div>
<script crossorigin="anonymous" integrity="sha384-+jvb+jCJ37FkNjPyYLI3KJzQeD8pPFXUra3B/QJFqQ3txYrUPIP1eOfxK4h3cKZP" src="https://lib.baomitu.com/vue/2.6.11/vue.js"></script>
<script crossorigin="anonymous" integrity="sha384-WbhdtWslh0AUD1Dhf8OExUvvjZ/VN6o2HHMsYlDXb6uf3IweMH13dGL4V/KgDc7y" src="https://lib.baomitu.com/element-ui/2.13.2/index.js"></script>
<script crossorigin="anonymous" integrity="sha512-quHCp3WbBNkwLfYUMd+KwBAgpVukJu5MncuQaWXgCrfgcxCJAq/fo+oqrRKOj+UKEmyMCG3tb8RB63W+EmrOBg==" src="https://lib.baomitu.com/axios/0.20.0/axios.min.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/common.js"></script>
<?php if (is_home() || is_front_page()) { ?>
	<script src="<?php bloginfo('template_url'); ?>/js/index.js"></script>
<?php } elseif(is_category() or (strpos($_SERVER['REQUEST_URI'], 'gamelist') != false)){ ?>
	<script src="<?php bloginfo('template_url'); ?>/js/category.js"></script>
<?php } elseif(is_category() or (strpos($_SERVER['REQUEST_URI'], 'newslist') != false)){ ?>
	<script src="<?php bloginfo('template_url'); ?>/js/news.js"></script>
<?php } else if (is_single()) { ?>
  <script crossorigin="anonymous" integrity="sha384-QUJ+ckWz1M+a7w0UfG1sEn4pPrbQwSxGm/1TIPyioqXBrwuT9l4f9gdHWLDLbVWI" src="https://lib.baomitu.com/quill/1.3.7/quill.min.js"></script>
  <script src="<?php bloginfo('template_url'); ?>/js/vue-quill-editor.js"></script>
  <script src="<?php bloginfo('template_url'); ?>/js/single.js"></script>
<?php } else { ?>

<?php } ?>
<script src="//at.alicdn.com/t/font_2133732_74plclthjc2.js"></script>
<?php wp_footer(); ?>
</body>
</html>
