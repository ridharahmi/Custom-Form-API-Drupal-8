<?php

namespace Drupal\custom_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;

/**
 * Defines FormControoler class.
 */
class FormController extends ControllerBase
{

    /**
     * preview Custom Form
     */
    public function preview()
    {
        $config = \Drupal::config('custom.settings');
        $fid = $config->get('image');
        if (!empty($fid)) {
            $file = File::load($fid[0]);
        }
        if (!empty($file)) {
            $url = $file->url();
        }

        $message['message']['header_image'] = drupal_get_path('module', 'custom_form') . '/assets/images/header.jpg';
        $message['message']['footer_image'] = drupal_get_path('module', 'custom_form') . '/assets/images/footer.jpg';
        $message['message']['content_type'] = $config->get('content_type');
        $message['message']['title'] = $config->get('title');
        $message['message']['email'] = $config->get('email');
        $message['message']['phone'] = $config->get('phone');
        $message['message']['date_birth'] = $config->get('date_birth');
        $message['message']['gender'] = $config->get('gender');
        $message['message']['radios'] = $config->get('radios');
        $message['message']['image'] = $url;
        $message['message']['description'] = $config->get('description');

        return array(
            '#theme' => 'preview_form',
            '#message' => $message,
        );
    }
}