<header>
    <div class="top-bar">
        <div class="container clearfix">
             <?php Dispatcher::instance()->dispatch('layout', 'languages', null, Request::HTML_REQUEST); ?>
        </div>
    </div>

    <div class="bottom-bar">
        <div class="container ">
            <div class="site-logo">
              <a href="<?php echo Conf::get('url'); ?>"><img src="<?php echo Conf::get('css_img_url'); ?>/logo 1.png" alt="logo"/><h1><?php echo Trans::get('Master LPFR')?></h1></a>
            </div>

        <?php Dispatcher::instance()->dispatch('layout', 'mainMenu', null, Request::HTML_REQUEST); ?>
        </div>



<!--    <div class="search-wrapper">-->
<!--      <button type="button" id="openSearchForm"><i class="fa fa-search"></i></button>-->
<!--    </div>-->

  </div>

</header>