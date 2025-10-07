<?php 
namespace Drupal\company\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
//use Drupal\Core\StringTranslation\StringTranslationTrait;



/**
 * Module settings form.
 */


class CompanyBanksForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'company_banks_form';
  }

  public function checkIBAN($iban)
  {
      if(strlen($iban) < 5) return false;
      $iban = strtolower(str_replace(' ','',$iban));
      $Countries = array('al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24);
      $Chars = array('a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,'l'=>21,'m'=>22,'n'=>23,'o'=>24,'p'=>25,'q'=>26,'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,'x'=>33,'y'=>34,'z'=>35);
  
      if(array_key_exists(substr($iban,0,2), $Countries) && strlen($iban) == $Countries[substr($iban,0,2)]){
                  
          $MovedChar = substr($iban, 4).substr($iban,0,4);
          $MovedCharArray = str_split($MovedChar);
          $NewString = "";
  
          foreach($MovedCharArray AS $key => $value){
              if(!is_numeric($MovedCharArray[$key])){
                  if(!isset($Chars[$MovedCharArray[$key]])) return false;
                  $MovedCharArray[$key] = $Chars[$MovedCharArray[$key]];
              }
              $NewString .= $MovedCharArray[$key];
          }
          
          if(bcmod($NewString, '97') == 1)
          {
              return true;
          }
      }
      return false;
  }
  

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Form constructor.
    $form = parent::buildForm($form, $form_state);
    // Load the configuration
    //$accounts = [];
    $initialized = $form_state->get('initialized');
    if (($initialized === NULL) Or ($initialized == FALSE)){
      $config = $this->config('company.banks');
      $accounts = $config->get('accounts');
      $form_state->set('initialized', TRUE);
    }
    else {
      $accounts = $form_state->getValue('accounts');
    }

    $account_count = count($accounts);
    $form_state->set('account_count', $account_count);
    
    $form['accounts'] = [
      '#type' => 'details',
      '#title' => 'Bank Accounts',
      '#prefix' => '<div id="accounts-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
      '#open' => TRUE,
    ];

    for ($i = 0; $i < $account_count; $i++) {
      $form['accounts']['account_' . $i] = [
        '#type' => 'fieldset',
        '#title' => $this->t(($i + 1) . '. Account'),
        '#prefix' => '<div id="account-wrapper_' . $i . '">',
        '#suffix' => '</div>',
        '#tree' => TRUE,
      ];

      $form['accounts']['account_' . $i]['bank'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Bank Name'),
        '#default_value' => isset($accounts['account_' . $i]['bank']) ? $accounts['account_' . $i]['bank'] : '',
      ];
      $form['accounts']['account_' . $i]['IBAN'] = [
        '#type' => 'textfield',
        '#title' => $this->t('IBAN'),
        '#default_value' => isset($accounts['account_' . $i]['IBAN']) ? $accounts['account_' . $i]['IBAN'] : '',
      ];
      $form['accounts']['account_' . $i]['description'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Description'),
        '#default_value' => isset($accounts['account_' . $i]['description']) ? $accounts['account_' . $i]['description'] : '',
      ];

      $form['accounts']['account_' . $i]['remove_account'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove this account'),
        '#submit' => ['::removeAccountCallback'],
        '#name' => 'remove_account_' . $i,
        '#ajax' => [
          'callback' => '::addAccountAjaxCallback',
          'wrapper' => 'accounts-wrapper',
        ],
      ];
  }

  $form['accounts']['add_account'] = [
    '#type' => 'submit',
    '#value' => $this->t('Add another account'),
    '#submit' => ['::addAccountCallback'],
    '#ajax' => [
      'callback' => '::addAccountAjaxCallback',
      'wrapper' => 'accounts-wrapper',
    ],
  ];

  // $v = $form_state->getValues();
  return $form;
}

public function addAccountCallback(array &$form, FormStateInterface $form_state) {
  $account_count = $form_state->get('account_count');
  $form_state->set('account_count', $account_count + 1);
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

public function removeAccountCallback(array &$form, FormStateInterface $form_state) {
  $triggering_element = $form_state->getTriggeringElement();
  $account_index_to_remove = str_replace('remove_account_', '', $triggering_element['#name']);

  $accounts = $form_state->getValue('accounts');
  unset($accounts['account_' . $account_index_to_remove]);
  unset($accounts['add_account']);
  //unset($accounts[3]);
  
  // Reset the accounts and account count
  $accounts = $this->ReindexKeys($accounts, 'account_');
  $form_state->setValue('accounts', $accounts);
  $form_state->set('account_count', count($accounts));
  // Reset the user input
  $inp = $form_state->getUserInput();
  $inp['accounts'] = $accounts;
  $form_state->setUserInput($inp);
  //$form_state->set('initialized', TRUE);

  $form_state->setRebuild(TRUE);
}

public function addAccountAjaxCallback(array &$form, FormStateInterface $form_state) {
  return $form['accounts'];
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
    $config = $this->config('company.banks');

    $accounts = [];
    $account_count = $form_state->get('account_count');
    for ($i = 0; $i < $account_count; $i++) {
      $accounts['account_' . $i]['bank'] = $form_state->getValue(['accounts', 'account_' . $i, 'bank']);
      $accounts['account_' . $i]['IBAN'] = $form_state->getValue(['accounts', 'account_' . $i, 'IBAN']);
      $accounts['account_' . $i]['description'] = $form_state->getValue(['accounts', 'account_' . $i, 'description']);
      
      // if (!empty($bank_name) && !empty($iban)) {
      //   $accounts['account_' . $i] = [
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
    $config->set('accounts', $accounts);
    $config->save();
    //$form_state->set('initialized', FALSE);
    return parent::submitForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'company.banks',
    ];
  }
}