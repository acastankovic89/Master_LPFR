<!DOCTYPE html>
<html lang="<?php Util::setHtmlTagLangAttr(); ?>">
  <head>
    <?php $this->displayMetaTitle(); ?>
    <?php $this->displayTemplate('templates/common/favicon.php', null); ?>
    <?php $this->displayTemplate('templates/common/meta.php', null); ?>
    <?php $this->displayAdditionalMetaTags(); ?>
    <?php $this->displayTemplate('templates/common/css.php', null); ?>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.6/jquery.fancybox.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo Conf::get('url'); ?>/css/pages/article.css"/>
  </head>
  <body id="articlePage" class="article-id-<?php echo $this->article->id; ?>">

    <?php $this->displayTemplate('templates/layout/header.php', null); ?>

    <?php $this->displayPage(); ?>

    <?php $this->displayTemplate('templates/layout/footer.php', null); ?>

    <?php $this->displayTemplate('templates/common/scripts.php', null); ?>
    <?php $this->displayTemplate('templates/common/constants.php', null); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.6/jquery.fancybox.min.js"></script>
    <script src="<?php echo Conf::get('url'); ?>/js/comments.js"></script>

  </body>
</html>