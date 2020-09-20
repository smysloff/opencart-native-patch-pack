<?php
class ControllerExtensionModuleFeedback extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/feedback');

        if (
            $this->request->server['REQUEST_METHOD'] == 'POST' &&
            $this->validate()
        ) {
            $mail = new Mail($this->config->get('config_mail_engine'));

            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($this->config->get('config_email'));
            $mail->setFrom($this->config->get('config_email'));
            $mail->setReplyTo($this->config->get('config_email'));
            $mail->setSender(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'));
            $mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['name']), ENT_QUOTES, 'UTF-8'));
            $mail->setText($this->request->post['phone']);

            $mail->send();

            $data['mail_success'] = $this->language->get('text_success');
        }

        $data['error_name']  = $this->error['name']  ?? '';
        $data['error_phone'] = $this->error['phone'] ?? '';

        $data['button_submit'] = $this->language->get('button_submit');

        $data['action'] = $this->url->link(
            $this->request->get['route'], '', true
        );

        $data['name'] =
            $this->request->post['name'] ??
            $this->customer->getFirstName();

        $data['phone'] =
            $this->request->post['phone'] ??
            $this->customer->getTelephone();

        // Maybe needs to add Captcha

        return $this->load->view('extension/module/feedback', $data);
    }

    protected function validate()
    {
        $name  = trim($this->request->post['name']);
        $phone = trim($this->request->post['phone']);

        if (
            (utf8_strlen($name) < 1) ||
            (utf8_strlen($name) > 32)
        ) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (
            (utf8_strlen($phone) < 1) ||
            (utf8_strlen($phone) > 32) ||
            !preg_match('/^[\d )(+-]+$/', $phone)
        ) {
            $this->error['phone'] = $this->language->get('error_phone');
        }

        // Maybe needs to add Captcha

        return !$this->error;
    }
}
