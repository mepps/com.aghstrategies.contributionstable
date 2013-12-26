<?php

require_once 'contributionstable.civix.php';
/**
 * Implementation of hook_civicrm_tokens
 */
function contributionstable_civicrm_tokens(&$tokens) {
  $tokens['contributions'] = array('contributions.itemized' => '');
}
/**
 * Implementation of hook_civicrm_tokensValues
 */
function contributionstable_civicrm_tokenValues( &$values, $cids, $job = null, $tokens = array(), $context = null ) {
  if (!empty($tokens['contributions'])){
    $contributions = array('contributions.itemized' => '');
    $rows = array();
    $header = "
        <h3>".ts("Contributions")."</h3>
        <table style='text-align:center'>
          <thead>
            <tr>
              <th>".ts("Date Received")."</th>
              <th>".ts("Tax Deductible Amount")."</th>              
            </tr>
          </thead>            
    ";
    foreach ($cids as $cid) {
      $dao = &CRM_Core_DAO::executeQuery("
      SELECT con.total_amount, (con.total_amount - con.non_deductible_amount) as deductible_amount, con.receive_date, cc.display_name
      FROM civicrm_contribution con
      LEFT JOIN civicrm_contact cc on con.contact_id=cc.id
      WHERE contact_id = ".$cid.
      " AND con.receive_date>=DATE_SUB(CURDATE(),INTERVAL 1 YEAR);"
      );
      $contributions_total = 0;
      while ($dao->fetch()) {
        $rows[] = '
          <tr>
            <td>' . date('m/d/Y', strtotime($dao->receive_date)) . '</td>
            <td>$' .($dao->deductible_amount). '</td>            
          </tr>
          ';
          $contributions_total += $dao->deductible_amount;
      }
      $contributions_total = "$".$contributions_total;
      $table = $header;
      if (!empty($rows)){
        foreach ($rows as $row){
          $table .= $row;
        }
      }
      $table .= "
        </table>
         <p>Your total contributions for the last year were ".$contributions_total.".</p>";
      $contributions = array('contributions.itemized' => $table);
      $values[$cid] = empty($values[$cid]) ? $contributions : $values[$cid] + $contributions;
    }  
  }
}

/**
 * Implementation of hook_civicrm_config
 */
function contributionstable_civicrm_config(&$config) {
  _contributionstable_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function contributionstable_civicrm_xmlMenu(&$files) {
  _contributionstable_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function contributionstable_civicrm_install() {
  return _contributionstable_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function contributionstable_civicrm_uninstall() {
  return _contributionstable_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function contributionstable_civicrm_enable() {
  return _contributionstable_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function contributionstable_civicrm_disable() {
  return _contributionstable_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function contributionstable_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _contributionstable_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function contributionstable_civicrm_managed(&$entities) {
  return _contributionstable_civix_civicrm_managed($entities);
}
