<?php

namespace Drupal\custom_form\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CustomForm extends ConfigFormBase
{
    /**
     * Config settings.
     *
     * @var string
     */
    const SETTINGS = 'custom.settings';

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            static::SETTINGS,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'custom_form';
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config(static::SETTINGS);

        $node_types = \Drupal\node\Entity\NodeType::loadMultiple();
        $options = [];
        foreach ($node_types as $node_type) {
            $options[$node_type->id()] = $node_type->label();
        }

        $form['content_type'] = [
            '#type' => 'select',
            '#options' => $options,
            '#title' => $this->t('Content type'),
            '#required' => TRUE,
            '#default_value' => $config->get('content_type'),
        ];

        $form['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#description' => $this->t('Enter the title of the book. Note that the title must be at least 10 characters in length.'),
            '#required' => TRUE,
            '#default_value' => $config->get('title'),

        ];
        $form['email'] = array(
            '#type' => 'email',
            '#title' => $this->t('Email'),
            '#required' => TRUE,
            '#default_value' => $config->get('email'),
        );
        $form['phone'] = array(
            '#type' => 'tel',
            '#title' => $this->t('Phone'),
            '#maxlength' => 15,
            '#default_value' => $config->get('phone'),
        );
        $form['date_birth'] = array(
            '#type' => 'date',
            '#title' => $this->t('Date of Birth'),
            '#required' => TRUE,
            '#default_value' => $config->get('date_birth'),
        );
        $form['gender'] = array(
            '#type' => 'select',
            '#title' => ('Gender'),
            '#options' => array(
                'male' => $this->t('Male'),
                'Female' => $this->t('Female'),
            ),
            '#default_value' => $config->get('gender'),
        );
        $form['radios'] = array(
            '#type' => 'radios',
            '#title' => $this->t('Radio Box'),
            '#options' => array(
                'Yes' => $this->t('Yes'),
                'No' => $this->t('No')
            ),
            '#default_value' => $config->get('radios'),
        );
        $form['description'] = [
            '#type' => 'text_format',
            '#format' => 'full_html',
            '#title' => $this->t('Description'),
            '#default_value' => $config->get('description'),
        ];
        $form['image'] = array(
            '#title' => $this->t('Custom Image'),
            '#description' => $this->t('Custom Image: png, jpg, git, jpeg'),
            '#type' => 'managed_file',
            '#upload_location' => 'public://custom-form/',
            '#upload_validators' => array(
                'file_validate_extensions' => array('gif png jpg jpeg'),
            ),
            '#default_value' => $config->get('image'),
        );


        $form['accept'] = array(
            '#type' => 'checkbox',
            '#title' => $this
                ->t('Send me a copy of the application.'),
            '#description' => $this->t('Please read and accept the terms of use'),
            '#default_value' => $config->get('accept'),
        );
        $form['actions'] = [
            '#type' => 'actions',
        ];

        $form['actions']['preview'] = [
            '#type' => 'submit',
            '#value' => $this->t('Save and Preview'),
            "#weight" => 1,
            '#button_type' => 'primary',
            '#submit' => array('::submitFormPreview'),
        ];

        return parent::buildForm($form, $form_state);
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);

        $title = $form_state->getValue('title');
        $phone = $form_state->getValue('phone');
        $accept = $form_state->getValue('accept');

        if (strlen($title) < 10) {
            $form_state->setErrorByName('title', $this->t('The title must be at least 10 characters long.'));
        }

        if (strlen($phone) < 8) {
            $form_state->setErrorByName('phone', $this->t('The Phone must be at least 8 number long.'));
        }

        if (empty($accept)) {
            $form_state->setErrorByName('accept', $this->t('You must accept the terms of use to continue'));
        }
    }

    /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->saveConfiguration($form_state);
        parent::submitForm($form, $form_state);
    }


    /**
     * {@inheritdoc}
     */
    public function submitFormPreview(array &$form, FormStateInterface $form_state)
    {
        $this->saveConfiguration($form_state);
        $url = Url::fromRoute('form.preview', []);
        $response = new RedirectResponse($url->toString());
        $response->send();
    }


    /**
     * @param FormStateInterface $form_state
     */
    private function saveConfiguration(FormStateInterface $form_state)
    {
        $this->config(static::SETTINGS)
            ->set('content_type', $form_state->getValue('content_type'))
            ->set('title', $form_state->getValue('title'))
            ->set('email', $form_state->getValue('email'))
            ->set('phone', $form_state->getValue('phone'))
            ->set('date_birth', $form_state->getValue('date_birth'))
            ->set('gender', $form_state->getValue('gender'))
            ->set('radios', $form_state->getValue('radios'))
            ->set('description', $form_state->getValue('description')['value'])
            ->set('image', $form_state->getValue('image'))
            ->set('accept', $form_state->getValue('accept'))
            ->save();

    }
}