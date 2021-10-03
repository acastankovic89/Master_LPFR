<!DOCTYPE html>
<html lang="<?php Util::setHtmlTagLangAttr(); ?>">
  <head>
    <?php $this->displayMetaTitle(); ?>
    <?php $this->displayTemplate('templates/common/favicon.php', null); ?>
    <?php $this->displayTemplate('templates/common/meta.php', null); ?>
    <?php $this->displayAdditionalMetaTags(); ?>
    <?php $this->displayTemplate('templates/common/css.php', null); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo Conf::get('url'); ?>/css/pages/404.css"/>
  </head>
  <body id="errorPage">

    <?php $this->displayTemplate('templates/layout/header.php', null); ?>

    <div class="container">

      <?php echo $this->renderSimpleBreadcrumbs($this->pageName); ?>

      <div class="error-wrapper">
        <h1>404</h1>
        <h2><?php echo Trans::get('Page not found'); ?></h2>
        <p><?php echo Trans::get('The page you are looking for was moved, removed, renamed or might never existed'); ?>.</p>
        <div><a href="<?php echo Conf::get('url'); ?>"><?php echo Trans::get('Back to home'); ?></a></div>
      </div>

    </div>

    <?php $this->displayTemplate('templates/layout/footer.php', null); ?>

    <?php $this->displayTemplate('templates/common/scripts.php', null); ?>
    <?php $this->displayTemplate('templates/common/constants.php', null); ?>

  </body>
</html>