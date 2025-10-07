<?php 
namespace Drupal\company\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
//use Drupal\Core\StringTranslation\StringTranslationTrait;



/**
 * Module settings form.
 */


class CompanyAddressesForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'company_addresses_form';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Form constructor.
    $form = parent::buildForm($form, $form_state);

    // Load the configuration
    //$addresses = [];
    $initialized = $form_state->get('initialized');
    if (($initialized === NULL) Or ($initialized == FALSE)){
      $config = $this->config('company.addresses');
      $addresses = $config->get('addresses');
      $form_state->set('initialized', TRUE);
    }
    else {
      $addresses = $form_state->getValue('addresses');
    }

    $address_count = count($addresses);
    $form_state->set('address_count', $address_count);
    
    $form['addresses'] = [
      '#type' => 'details',
      '#title' => 'Company addresses',
      '#prefix' => '<div id="addresses-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
      '#open' => TRUE,
    ];

    for ($i = 0; $i < $address_count; $i++) {
      $form['addresses']['address_' . $i] = [
        '#type' => 'fieldset',
        '#title' => $this->t(($i + 1) . '. address'),
        '#prefix' => '<div id="address-wrapper_' . $i . '">',
        '#suffix' => '</div>',
        '#tree' => TRUE,
      ];

      $form['addresses']['address_' . $i]['street'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Street'),
        '#default_value' => isset($addresses['address_' . $i]['street']) ? $addresses['address_' . $i]['street'] : '',
      ];
      $form['addresses']['address_' . $i]['street_number'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Street number'),
        '#default_value' => isset($addresses['address_' . $i]['street_number']) ? $addresses['address_' . $i]['street_number'] : '',
      ];
      $form['addresses']['address_' . $i]['city'] = [
        '#type' => 'textfield',
        '#title' => $this->t('City'),
        '#default_value' => isset($addresses['address_' . $i]['city']) ? $addresses['address_' . $i]['city'] : '',
      ];
      $form['addresses']['address_' . $i]['city_code'] = [
        '#type' => 'textfield',
        '#title' => $this->t('City code'),
        '#default_value' => isset($addresses['address_' . $i]['city_code']) ? $addresses['address_' . $i]['city_code'] : '',
      ];

      $form['addresses']['address_' . $i]['remove_address'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove this address'),
        '#submit' => ['::removeaddressCallback'],
        '#name' => 'remove_address_' . $i,
        '#ajax' => [
          'callback' => '::addaddressAjaxCallback',
          'wrapper' => 'addresses-wrapper',
        ],
      ];
  }

  $form['addresses']['add_address'] = [
    '#type' => 'submit',
    '#value' => $this->t('Add another address'),
    '#submit' => ['::addaddressCallback'],
    '#ajax' => [
      'callback' => '::addaddressAjaxCallback',
      'wrapper' => 'addresses-wrapper',
    ],
  ];

  // $v = $form_state->getValues();
  return $form;
}

public function addaddressCallback(array &$form, FormStateInterface $form_state) {
  $address_count = $form_state->get('address_count');
  $form_state->set('address_count', $address_count + 1);
  $form_state->setRebuild(TRUE);
}

protected function ReindexKeys(array &$arr, string $prefix) {
  $i = 0;
  $keys = array_keys($arr);
  $values = array_values($arr);
  
  foreach ($keys as $key) {
    $keys[$i] = $prefix . $i;
    $i++;
  }

  $array = array_combine($keys, $values);
  return $array;
}

public function removeaddressCallback(array &$form, FormStateInterface $form_state) {
  $triggering_element = $form_state->getTriggeringElement();
  $address_index_to_remove = str_replace('remove_address_', '', $triggering_element['#name']);

  $addresses = $form_state->getValue('addresses');
  unset($addresses['address_' . $address_index_to_remove]);
  unset($addresses['add_address']);
  //unset($addresses[3]);
  
  // Reset the addresses and address count
  $addresses = $this->ReindexKeys($addresses, 'address_');
  $form_state->setValue('addresses', $addresses);
  $form_state->set('address_count', count($addresses));
  // Reset the user input
  $inp = $form_state->getUserInput();
  $inp['addresses'] = $addresses;
  $form_state->setUserInput($inp);
  //$form_state->set('initialized', TRUE);

  $form_state->setRebuild(TRUE);
}

public function addaddressAjaxCallback(array &$form, FormStateInterface $form_state) {
  return $form['addresses'];
}


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // if ($form_state->getValue('name') == NULL) {
    //   $form_state->setErrorByName('name', 'Please enter a valid name.');
    // }
    // if ($this->checkIBAN($form_state->getValue('IBAN')) == FALSE) {
    //   $form_state->setErrorByName('IBAN', t('Please enter correct IBAN Code.'));
    // }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('company.addresses');

    $addresses = [];
    $address_count = $form_state->get('address_count');
    for ($i = 0; $i < $address_count; $i++) {
      $addresses['address_' . $i]['street'] = $form_state->getValue(['addresses', 'address_' . $i, 'street']);
      $addresses['address_' . $i]['street_number'] = $form_state->getValue(['addresses', 'address_' . $i, 'street_number']);
      $addresses['address_' . $i]['city'] = $form_state->getValue(['addresses', 'address_' . $i, 'city']);
      $addresses['address_' . $i]['city_code'] = $form_state->getValue(['addresses', 'address_' . $i, 'city_code']);
      
      // if (!empty($bank_name) && !empty($iban)) {
      //   $addresses['address_' . $i] = [
      //     'bank' => $bank_name,
      //     'IBAN' => $iban,
      //     'description' => $description,
      //   ];
      // }
    }

    // $config->set('name', $form_state->getValue('name'));
    // $config->set('street', $form_state->getValue('street'));
    // $config->set('city', $form_state->getValue('city'));
    // $config->set('PSC', $form_state->getValue('PSC'));
    // $config->set('ICO', $form_state->getValue('ICO'));
    // $config->set('DIC', $form_state->getValue('DIC'));
    // $config->set('RegNo', $form_state->getValue('RegNo'));
    $config->set('addresses', $addresses);
    $config->save();
    //$form_state->set('initialized', FALSE);
    return parent::submitForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'company.addresses',
    ];
  }
}