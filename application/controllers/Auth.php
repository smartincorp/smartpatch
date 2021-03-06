<?php
/**
 * @owner xXAlphaManXx
 * @editedOn 03/12/16
 * @package Smart Patch
 * @file Auth.php
 *
 * Licensed to Smart Hacks Inc.
 * @link https://smarthacksinc.com
 *
 * @repo https://github.com/smarthacks/
 *
 */
defined ('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    public function login () {

        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|max_length[15]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if($this->form_validation->run() == false) {
            $this->load->view('auth/login', array('error' => 'Oops, the required fields are not filled'));
        } else {
            $this->load->model('Auth', '', TRUE);
            $res = file_get_contents('http://ip-api.com/json');
            $json = json_decode($res);
            $data = array(
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
                'email'    => $this->input->post('email'),
                'acctype'  => 'free',
                'ip'       => $json->{'query'},
                'country'  => $json->{'country'},
                'city'     => $json->{'city'},
                'state'    => $json->{'regionName'}
            );

            $this->Auth->loginUser($data);
        }
    }

    public function register() {
        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('<div class="callout callout-danger">', '</div>');

        $this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|max_length[15]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|min_length[10]|max_length[30]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/register', array('error' => 'Oops, please try again by filling all 
                                                                                        required fields'));
        } else {

            $res = file_get_contents('http://ip-api.com/json');
            $json = json_decode($res);
            $hash = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
            $data = array(
                'username' => $this->input->post('username'),
                'password' => $hash,
                'email'    => $this->input->post('email'),
                'acctype'  => 'free',
                'ip'       => $json->{'query'},
                'country'  => $json->{'country'},
                'city'     => $json->{'city'},
                'state'    => $json->{'regionName'}
            );

            $this->load->model('Auth', '', TRUE);
            $this->Auth->checkUser($this->input->post('username'),
                                    $this->input->post('email'),
                                    $data);
        }
    }
}