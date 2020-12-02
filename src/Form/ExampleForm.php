<?php

namespace Drupal\d8_custom_form_using_validator\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

/**
 * Class ExampleForm.
 */
class ExampleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'example_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['text_validation'] = [
      '#type'      => 'textfield',
      '#title'     => $this->t('Text Validation'),
      '#maxlength' => 64,
      '#size'      => 64,
      '#weight'    => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $validator = Validation::createValidator();

    foreach ($this->getFieldsValidators() as $field => $rules) {
      // Validate all validators for field.
      $field_value = $form_state->getValue($field);
      $violations = $validator->validate($field_value, $rules);
      if (0 !== count($violations)) {
        foreach ($violations as $violation) {
          $form_state->setErrorByName($field, $violation->getMessage());
        }
      }
    }
    parent::validateForm($form, $form_state);
  }

  private function getFieldsValidators() {
    return [
      'text_validation' => [
        new Length([
          'min' => 5,
          'max' => 10,
        ]),
        new NotBlank(),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }
  }

}
