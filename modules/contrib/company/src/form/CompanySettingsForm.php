<?php 
namespace Drupal\company\form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
//use Drupal\Core\StringTranslation\StringTranslationTrait;



/**
 * Module settings form.
 */


class CompanySettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'company_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Form constructor.
    $form = parent::buildForm($form, $form_state);

    // Load the configuration
    $config = \Drupal::service('config.factory')->get('company.settings');
    //$config = $this->config('company.settings');
    $typed_config = \Drupal::service('config.typed');
    $schema = $typed_config->getDefinition('company.settings');

    //\Drupal::logger('company')->notice('<pre>' . print_r($config->getRawData(), TRUE) . '</pre>');
    // Page title field

    // if ($schema && isset($schema['mapping'])) {
    //   foreach ($schema['mapping'] as $key => $definition) {
    //     $form[$key] = [
    //       '#type' => ($definition['type'] === 'boolean') ? 'checkbox' : 'textfield',
    //       '#title' => $this->t($definition['label'] ?? $key),
    //       '#default_value' => $config->get($key),
    //       '#description' => $definition['description'] ?? '',
    //     ];
    //   }
    // }

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $schema['mapping']['name']['label'],
      '#default_value' => $config->get('name'),
      '#description' => 'Provide the name of the company.',
    ];

    $form['ICO'] = [
      '#type' => 'textfield',
      '#title' => 'IČO:',
      '#default_value' => $config->get('ICO'),
    ];

    $form['DIC'] = [
      '#type' => 'textfield',
      '#title' => 'DIČ:',
      '#default_value' => $config->get('DIC'),
    ];

    $form['RegNo'] = [
      '#type' => 'textfield',
      '#title' => 'Registračné číslo:',
      '#default_value' => $config->get('RegNo'),
    ];

  return $form;
}


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('name') == NULL) {
      $form_state->setErrorByName('name', 'Please enter a valid name.');
    }
    // if ($this->checkIBAN($form_state->getValue('IBAN')) == FALSE) {
    //   $form_state->setErrorByName('IBAN', t('Please enter correct IBAN Code.'));
    // }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('company.settings');

    $config->set('name', $form_state->getValue('name'));
    $config->set('ICO', $form_state->getValue('ICO'));
    $config->set('DIC', $form_state->getValue('DIC'));
    $config->set('RegNo', $form_state->getValue('RegNo'));
    $config->save();

    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'company.settings',
    ];
  }
}