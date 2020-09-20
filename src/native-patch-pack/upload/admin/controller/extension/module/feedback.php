<?php
class ControllerExtensionModuleFeedback extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/feedback');

        $this->document->setTitle($this->language->get('heading_title'));


        /**
         * INSTALLATION
         */

        $this->load->model('setting/setting');

        if (
            ($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()
        ) {
            $this->model_setting_setting->editSetting('module_feedback', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data['error_warning'] = isset($this->error['warning']) ?
            $this->error['warning'] : '';


        /**
         * BREADCRUMBS
         */

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/feedback', 'user_token=' . $this->session->data['user_token'], true)
        );


        /**
         * TOP-RIGHT BUTTONS
         */

        $data['action'] = $this->url->link('extension/module/feedback', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);


        /**
         *  MODULE STATUS
         */
        
        $data['module_feedback_status'] =
            $this->request->post['module_feedback_status'] ??
            $this->config->get('module_feedback_status');


        /**
         * STATIC CONTENT
         */

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');


        /**
         * VIEW
         */

        $this->response->setOutput($this->load->view('extension/module/feedback', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/feedback')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
