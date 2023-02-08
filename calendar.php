<?php

require_once 'civicalendar.civix.php';
// phpcs:disable
use CRM_Civicalendar_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function civicalendar_civicrm_config(&$config) {
  _civicalendar_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function civicalendar_civicrm_install() {
  _civicalendar_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function civicalendar_civicrm_postInstall() {
  _civicalendar_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function civicalendar_civicrm_uninstall() {
  _civicalendar_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function civicalendar_civicrm_enable() {
  _civicalendar_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function civicalendar_civicrm_disable() {
  _civicalendar_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function civicalendar_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civicalendar_civix_civicrm_upgrade($op, $queue);
}


/**
 * Implements hook_civicrm_tabset().
 */
function calendar_civicrm_tabset($path, &$tabs, $context) {
  if ($path === 'civicrm/contact/view') {
    $url = CRM_Utils_System::url('civicrm/calendar', ['cid' => $context['contact_id']]);

    $tabs[] = [
      'id' => 'calendar',
      'url' => $url,
      'title' => E::ts('Calendar'),
      'weight' => 15,
      'icon' => 'crm-i fa-calendar-check-o',
    ];

    _calendar_civix_addJSCss();
  }
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
 */
function calendar_civicrm_navigationMenu(&$menu) {
  $civiCalendarSettings = [
    'name' => 'Calendar Settings',
    'url' => 'civicrm/admin/calendar',
    'permission' => 'administer CiviCRM',
    'operator' => NULL,
    'separator' => NULL,
  ];
  _calendar_civix_insert_navigation_menu($menu, 'Administer/', $civiCalendarSettings);
}

/**
 * @param $contactID
 * @param $contentPlacement
 *
 * @return string
 */
function calendar_civicrm_dashboard($contactID, &$contentPlacement) {
  $isOnDashboard = CRM_Core_DAO::singleValueQuery('
        SELECT count(*)
        FROM civicrm_dashboard
        WHERE `name` = "calendar"
        AND is_active=1
        AND domain_id IN (select MIN(id) from civicrm_domain)
      ');

  if ($isOnDashboard) {
    _calendar_civix_addJSCss();
  }

  return '';
}

/**
 * Implements hook_civicrm_pageRun().
 */
function calendar_civicrm_pageRun(&$page) {
  $pageName = $page->getVar('_name');

  if ($pageName == 'CRM_Event_Page_EventInfo' && CRM_Core_Permission::check('register for events')) {
    CRM_Core_Region::instance('page-body')->add([
      'template' => CRM_Calendar_ExtensionUtil::path() . '/templates/CRM/Calendar/Page/Field/EventInfo.tpl'
    ]);
  }
}

/**
 * Adding css and js files to page body
 */
function _calendar_civix_addJSCss() {
  CRM_Core_Resources::singleton()
    ->addStyleFile('com.agiliway.civicalendar', 'css/fullcalendar.min.css', 200, 'html-header');
  CRM_Core_Resources::singleton()
    ->addStyleFile('com.agiliway.civicalendar', 'css/calendar.css', 201, 'html-header');

  CRM_Core_Resources::singleton()
    ->addScriptFile('com.agiliway.civicalendar', 'js/tooltip.js', 199, 'html-header');
  CRM_Core_Resources::singleton()
    ->addScriptFile('com.agiliway.civicalendar', 'js/popover.js', 200, 'html-header');
  CRM_Core_Resources::singleton()
    ->addScriptFile('com.agiliway.civicalendar', 'js/moment.min.js', 200, 'html-header');
  CRM_Core_Resources::singleton()
    ->addScriptFile('com.agiliway.civicalendar', 'js/fullcalendar.min.js', 201, 'html-header');
  CRM_Core_Resources::singleton()
    ->addScriptFile('com.agiliway.civicalendar', 'js/locale-all.js', 201, 'html-header');

  if (CRM_Calendar_Settings::isShoreditch()) {
    CRM_Core_Resources::singleton()
      ->addStyleFile('com.agiliway.civicalendar', 'css/shoreditch-fix.css', 199, 'html-header');
  }

  CRM_Core_Resources::singleton()->addVars('datepicker_locale', ['language' => CRM_Calendar_Utils_Locale::getCurrentLocaleForCalendar()]);
  CRM_Core_Region::instance('page-header')->add([
    'scriptUrl' => CRM_Calendar_ExtensionUtil::url('js/DatepickerFix.js'),
  ]);
}
