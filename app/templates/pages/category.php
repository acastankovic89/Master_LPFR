<!DOCTYPE html>
<html lang="<?php Util::setHtmlTagLangAttr(); ?>">
  <head>
    <?php echo $this->displayMetaTitle(); ?>
    <?php $this->displayTemplate('templates/common/favicon.php', null); ?>
    <?php $this->displayTemplate('templates/common/meta.php', null); ?>
    <?php $this->displayAdditionalMetaTags(); ?>
    <?php $this->displayTemplate('templates/common/css.php', null); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo Conf::get('url'); ?>/css/pages/category.css" />
  </head>
  <body id="categoryPage" class="category-id-<?php echo $this->category->id; ?>">

    <?php $this->displayTemplate('templates/layout/header.php', null); ?>

    <?php $this->displayPage(); ?>

    <?php $this->displayTemplate('templates/layout/footer.php', null); ?>

    <?php $this->displayTemplate('templates/common/scripts.php', null); ?>
    <?php $this->displayTemplate('templates/common/constants.php', null); ?>

  </body>
</html>