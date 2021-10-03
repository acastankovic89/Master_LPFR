<!DOCTYPE html>
<html lang="<?php Util::setHtmlTagLangAttr(); ?>">
  <head>
    <?php $this->displayMetaTitle(); ?>
    <?php $this->displayTemplate('templates/common/favicon.php', null); ?>
    <?php $this->displayTemplate('templates/common/meta.php', null); ?>
    <?php $this->displayAdditionalMetaTags(); ?>
    <?php $this->displayTemplate('templates/common/css.php', null); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo Conf::get('url'); ?>/css/pages/contact.css"/>
  </head>
  <body id="contactPage">

    <?php $this->displayTemplate('templates/layout/header.php', null); ?>

    <div class="container">

      <?php $this->renderSimpleBreadcrumbs($this->pageName); ?>

      <div class="map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2830.306695965634!2d20.456470615535835!3d44.81531617909868!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x475a7ab262d773e9%3A0xec94a4d857dffae8!2sObili%C4%87ev+venac+19%2C+Beograd!5e0!3m2!1sen!2srs!4v1563538004948!5m2!1sen!2srs" frameborder="0" allowfullscreen></iframe>
      </div>

      <h2><?php echo Trans::get('Send us E-mail'); ?></h2>

      <form id="contactForm">

        <div class="form-wrapper">

          <div class="field-wrapper">
            <?php echo Form::label(Trans::get('Name'), 'contactName'); ?>
            <?php echo Form::field('text', 'name', '', array('idName' => 'contactName', 'required' => true)); ?>
          </div>

          <div class="field-wrapper">
            <?php echo Form::label(Trans::get('E-mail'), 'contactEmail'); ?>
            <?php echo Form::field('text', 'email', '', array('idName' => 'contactEmail', 'required' => true)); ?>
          </div>

          <div class="field-wrapper">
            <?php echo Form::label(Trans::get('Phone'), 'contactPhone'); ?>
            <?php echo Form::field('text', 'phone', '', array('idName' => 'contactPhone', 'required' => true)); ?>
          </div>

          <div class="field-wrapper big textarea-wrapper">
            <?php echo Form::label(Trans::get('Message'), 'contactMessage'); ?>
            <?php echo Form::textarea('message', '', array('idName' => 'contactMessage', 'required' => true)); ?>
          </div>

          <div class="form-section form-buttons big">
            <button type="button" class="form-btn-clear"><?php echo Trans::get('Clear'); ?></button>
            <button type="submit" class="form-btn-submit"><?php echo Trans::get('Send'); ?></button>
          </div>

          </div>

        </form>

      </div>

    <?php $this->displayTemplate('templates/layout/footer.php', null); ?>

    <?php $this->displayTemplate('templates/common/scripts.php', null); ?>
    <?php $this->displayTemplate('templates/common/constants.php', null); ?>
    <script src="<?php echo Conf::get('url') . '/js/pages/contact.js' ?>"></script>

  </body>
</html>