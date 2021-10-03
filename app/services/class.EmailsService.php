<?php

class EmailsService {

  private $view;

  public function __construct() {
    $this->view = new EmailsView();
  }

  public function accountActivationData($item) {

    $template = $this->view->userActivationTemplate($item);

    $data = array();
    $data['form_address'] = Conf::get('mail_from_address');
    $data['form_name'] = Conf::get('mail_from_name');
    $data['to_address'] = $item->email;
    $data['subject'] = Trans::get('User profile activation');
    $data['html_body'] = $template['html_body'];
    $data['alt_body'] = $template['alt_body'];

    return $data;
  }

  public function resetPasswordData($item) {

    $template = $this->view->resetPasswordTemplate($item);

    $data = array();
    $data['form_address'] = Conf::get('mail_from_address');
    $data['form_name'] = Conf::get('mail_from_name');
    $data['to_address'] = $item->email;
    $data['subject'] = Trans::get('Password changing');
    $data['html_body'] = $template['html_body'];
    $data['alt_body'] = $template['alt_body'];

    return $data;
  }

  public function contactFormData($params) {

    $template = $this->view->contactFormTemplate($params);

    $data = array();
    $data['form_address'] = Conf::get('mail_from_address');
    $data['form_name'] = Conf::get('mail_from_name');
    $data['to_address'] = Conf::get('mail_to_address');
    $data['subject'] = Trans::get('Contact form');
    $data['html_body'] = $template['html_body'];
    $data['alt_body'] = $template['alt_body'];

    return $data;
  }

  public function commentFormData($params) {

    $template = $this->view->commentFormTemplate($params);

    $data = array();
    $data['form_address'] = Conf::get('mail_from_address');
    $data['form_name'] = Conf::get('mail_from_name');
    $data['to_address'] = Conf::get('mail_to_address');
    $data['subject'] = Trans::get('Comment');
    $data['html_body'] = $template['html_body'];
    $data['alt_body'] = $template['alt_body'];

    return $data;
  }

  public function newsletterCustomerData($params) {

    $template = $this->view->newsletterCustomerTemplate();

    $data = array();
    $data['form_address'] = Conf::get('mail_from_address');
    $data['form_name'] = Conf::get('mail_from_name');
    $data['to_address'] = $params['email'];
    $data['subject'] = Trans::get('Newsletter Signup');
    $data['html_body'] = $template['html_body'];
    $data['alt_body'] = $template['alt_body'];

    return $data;
  }

  public function newsletterAdminData($params) {

    $template = $this->view->newsletterAdminTemplate($params);

    $data = array();
    $data['form_address'] = Conf::get('mail_from_address');
    $data['form_name'] = Conf::get('mail_from_name');
    $data['to_address'] = Conf::get('mail_to_address');
    $data['subject'] = Trans::get('Newsletter Signup');
    $data['html_body'] = $template['html_body'];
    $data['alt_body'] = $template['alt_body'];

    return $data;
  }
}

?>