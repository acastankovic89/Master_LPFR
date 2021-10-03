<!DOCTYPE html>
<html lang="<?php Util::setHtmlTagLangAttr(); ?>">
  <head>
    <?php $this->displayMetaTitle(); ?>
    <?php $this->displayTemplate('templates/common/favicon.php', null); ?>
    <?php $this->displayTemplate('templates/common/meta.php', null); ?>
    <?php $this->displayAdditionalMetaTags(); ?>
    <?php $this->displayTemplate('templates/common/css.php', null); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo Conf::get('url'); ?>/public/plugins/nc/slider/nc-slider.min.css"/>
  </head>
  <body id="homePage">

    <?php $this->displayTemplate('templates/layout/header.php', null); ?>

<!--    --><?php //Dispatcher::instance()->dispatch('layout', 'homeSlider', null, Request::HTML_REQUEST); ?>

   <?php $this->displayAboutSection();?>

    <?php $this->displayServiceSection()?>

    <?php $this->displayFaqSection()?>

    <div id="contactPage">

    <div class="container">

        <h1><?php echo Trans::get('Kontakt:'); ?></h1>
        <p><?php echo Trans::get('Master Software d.o.o.'); ?></p>
        <p><?php echo Trans::get('Milutina Milankovića 9ž'); ?></p>
        <p><?php echo Trans::get('11000 Beograd'); ?></p>
        <p></p>
        <p><?php echo Trans::get('Republika Srbija'); ?></p>

        <div class="buttons">
            <p class="buttons-item"><?php echo Trans::get('office@mastersoftware.rs'); ?></p>
            <p class="buttons-item"><?php echo Trans::get('011  4 405 409'); ?></p>
            <p class="buttons-item"><?php echo Trans::get('1061  4 405 409'); ?></p>
            <a class="buttons-item-spec" href="#"><?php echo Trans::get('Brošura'); ?></a>
            <img src="<?php echo Conf::get('url')?>/css/img/skica 6.png">
        </div>

        <p><?php echo Trans::get("Ako imate neka pitanja, ili želite da budete u toku sa najnovijim informacijama vezanim za Master L-PFR, pošaljite nam poruku:")?></p>


        <form id="contactForm">

            <div class="form-wrapper">

                <div class="field-wrapper">
                    <?php echo Form::label(Trans::get('Ime i prezime'), 'contactName'); ?>
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

                <div class="field-wrapper">
                    <?php echo Form::label(Trans::get('Ime firme'), 'companyName'); ?>
                    <?php echo Form::field('text', 'companyName', '', array('idName' => 'contactCompanyName', 'required' => true)); ?>
                </div>

                <div class="field-wrapper big textarea-wrapper">
                    <?php echo Form::label(Trans::get('Message'), 'contactMessage'); ?>
                    <?php echo Form::textarea('message', '', array('idName' => 'contactMessage', 'required' => true)); ?>
                </div>

                <div class="form-section form-buttons big">

                    <button type="submit" class="form-btn-submit"><?php echo Trans::get('Send'); ?></button>
                </div>

            </div>

        </form>

    </div>
    </div>
    </div>




    <?php //$this->displayPage(); ?>

    <?php $this->displayTemplate('templates/layout/footer.php', null); ?>

    <?php $this->displayTemplate('templates/common/scripts.php', null); ?>
    <?php $this->displayTemplate('templates/common/constants.php', null); ?>
    <script src="<?php echo Conf::get('url') . '/js/pages/contact.js' ?>"></script>
    <script src="<?php echo Conf::get('url'); ?>/public/plugins/nc/slider/nc-slider.min.js"></script>
    <script>
      $('#slider').ncSlider();
    </script>
  </div>
</html>