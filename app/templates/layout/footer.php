<footer>
  <?php echo Conf::get('site_name'); ?> © <?php echo date('Y') . ', ' . Trans::get('All rights reserved') . ' | ' . Trans::get('Powered by'); ?> <a href="http://normasoft.net/" target="_blank">Normasoft</a>
</footer>

<div id="overlay" class="page-overlay"></div>
<div id="loader">
  <div class="loader-dots">
    <span></span><span></span><span></span>
  </div>
</div>

<a id="backTop"><i></i></a>

<?php $this->displaySearchForm(); ?>