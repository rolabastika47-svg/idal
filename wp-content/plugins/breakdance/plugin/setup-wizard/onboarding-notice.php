<?php

namespace Breakdance\SetupWizard\Onboarding;

use function Breakdance\Data\get_global_option;
use function Breakdance\Data\set_global_option;
use function Breakdance\DesignLibrary\hideDesignLibrary;
use function Breakdance\Setup\hasPluginAlreadyBeenActivated;
use function Breakdance\Util\is_post_request;

add_action('admin_notices', '\Breakdance\SetupWizard\Onboarding\notice');

function notice()
{

  if (BREAKDANCE_MODE === 'oxygen') {
    return;
  }

  if (!get_current_screen()) {
    return;
  }

  /** @var \WP_Screen $screen */
  $screen = get_current_screen();

  if ($screen->id !== 'dashboard') {
    return;
  }

  if (hasPluginAlreadyBeenActivated()) {
    return;
  }

  if (is_post_request() && check_admin_referer('bd_onboading_notice')) {
    $dismiss = (string) filter_input(INPUT_POST, 'bd_notice_dismiss');

    if ($dismiss) {
      set_global_option('notice_dismiss', 'true');
    }

    return;
  }

  if (get_global_option('notice_dismiss') === 'true') {
    return;
  }

  showNotice(true, true);
}


/**
 * @param bool $includeFooter
 * @param bool $showDismiss
 * @return void
 */
function showNotice($includeFooter, $showDismiss)
{
?>
  <style>
    .bd-setup-notice {
      box-shadow: 0px 1px 1px 0px #00000014;

      border: 1px solid #a3a3a3;
      padding: 0;
    }

    .bd-setup-notice,
    .bd-setup-notice__top,
    .bd-setup-notice__bottom {
      border-radius: 4px;
    }

    .bd-setup-notice h2,
    .bd-setup-notice h3 {
      margin: 0;
    }

    .bd-setup-notice__top,
    .bd-setup-notice__mid {
      padding: 32px;
    }

    .bd-setup-notice__top {
      background: #fafafa;
      display: flex;
      justify-content: space-between;
    }

    .bd-setup-notice__top-content {
      display: flex;
      gap: 16px;
    }

    .bd-setup-notice__top-text h2 {
      font-size: 24px;
      letter-spacing: -0.02em;
      margin-bottom: 4px;
    }

    .bd-setup-notice__top-text-secondary {
      margin: 0;
      font-size: 14px;
      color: hsl(0deg 0% 45%);
    }

    .bd-setup-notice__bottom {
      padding: 16px 32px;
      background: #f5f5f5;
      display: flex;
      gap: 8px;
    }

    .bd-setup-notice__bottom-text {
      color: hsl(0deg 0% 64%);
      font-size: 14px;
      text-decoration: none;
    }

    .bd-setup-notice__bottom-text:hover {
      color: hsl(0deg 0% 50%)
    }

    .bd-setup-notice__mid {
      display: flex;
      gap: 24px;
      flex-wrap: wrap;
    }

    .bd-setup-notice__card {
      display: flex;
      gap: 16px;
      flex-direction: column;
      max-width: 240px;
    }

    .bd-setup-notice__card h3 {
      font-size: 16px;
    }

    .bd-setup-notice__card-text {
      font-size: 14px;
      color: #525252;
    }

    .bd-setup-notice__card-button-wrapper {
      display: flex;
    }

    .bd-setup-notice__card-button {
      border-radius: 3px;
      padding: 8px 16px 8px 16px;
      border: none;
      font-weight: 600;
      text-decoration: none;
      color: #000;
      transition: all .3s ease;
    }

    .bd-setup-notice__card-button:hover {
      color: #000;
    }

    .bd-setup-notice__card-button-primary {
      background: #facc15;
    }

    .bd-setup-notice__card-button-primary:hover {
      background: #EAB308;
    }

    .bd-setup-notice__card-button-secondary {
      border: 1px solid hsl(0deg 0% 90%);
      background: #fff;

    }

    .bd-setup-notice__card-button-secondary:hover {
      background: #E5E5E5;
    }

    .bd-setup-notice__top-dismiss {
      cursor: pointer;
      background: transparent;
      border: none;
      padding: 0;
    }

    .bd-setup-notice__top-dismiss:hover {
      color: hsl(0deg 0% 25%)
    }

    .bd-setup-notice__card-tag {

      padding: 2px 4px;
      border-radius: 3px;
      border: 1px solid #e5e5e5;
      font-size: 12px;
      color: #000;
      font-weight: 500;
    }

    .bd-setup-notice__text-wrap {
      display: flex;
      gap: 8px;
      align-items: center;
    }
  </style>


  <div class="bd-setup-notice notice">
    <div class="bd-setup-notice__top">
      <div class="bd-setup-notice__top-content">
        <svg
          width="34"
          height="34"
          viewBox="0 0 34 34"
          fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <rect width="34" height="34" rx="3.12" fill="#FACC15" />
          <path
            d="M17.2678 20.2569L11.2113 20.2571C10.5455 20.2567 10.0001 19.6924 10 19.0309C10.0005 18.3696 10.5459 17.8057 11.2115 17.8055H13.6422C14.3047 17.8007 14.8452 17.2381 14.8454 16.5797V9.22546C14.8458 8.56427 15.391 8.00042 16.0565 8C16.7221 8.00028 17.2674 8.56417 17.2678 9.22546V12.6916C17.2682 13.3437 17.8413 13.9167 18.4788 13.9171C19.1445 13.9168 19.6898 13.3528 19.6901 12.6914V9.22546C19.6905 8.56417 20.2358 8.00028 20.9014 8C21.567 8.00028 22.1124 8.56413 22.1129 9.22546V23.9343C22.1125 24.5957 21.5671 25.1597 20.9014 25.16C20.2357 25.1597 19.6904 24.5957 19.6901 23.9343V22.7086C19.6901 21.3553 18.6046 20.2569 17.2678 20.2569Z"
            fill="black" />
          <path
            d="M16.991 24.7353C17.9458 24.7353 18.7198 23.952 18.7198 22.9859C18.7198 22.0197 17.9458 21.2364 16.991 21.2364C16.0362 21.2364 15.2621 22.0197 15.2621 22.9859C15.2621 23.952 16.0362 24.7353 16.991 24.7353Z"
            fill="black" />
        </svg>

        <div class="bd-setup-notice__top-text">
          <h2><?php esc_html_e('Welcome to Breakdance!', 'breakdance'); ?></h2>
          <p class="bd-setup-notice__top-text-secondary">
            <?php esc_html_e('Let\'s get you up to speed.', 'breakdance'); ?>
          </p>
        </div>
      </div>

      <?php
      if ($showDismiss) {
      ?>
        <form action="" method="post">
          <?php
          wp_nonce_field('bd_onboading_notice'); ?>
          <input name="bd_notice_dismiss" value="true" hidden />

          <button
            class="bd-setup-notice__top-dismiss bd-setup-notice__top-text-secondary">
            <?php esc_html_e('Dismiss', 'breakdance'); ?>
          </button>
        </form>
      <?php
      }
      ?>
    </div>
    <div class="bd-setup-notice__mid">
      <?php if (!hideDesignLibrary()) { ?>
        <div class="bd-setup-notice__card">
        <svg
          width="11"
          height="11"
          viewBox="0 0 11 11"
          fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path
            d="M11 1.25V8.75C11 9.1875 10.6562 9.5 10.25 9.5C9.8125 9.5 9.5 9.1875 9.5 8.75V3.0625L2.28125 10.2812C2.125 10.4375 1.9375 10.5 1.75 10.5C1.53125 10.5 1.34375 10.4375 1.21875 10.2812C0.90625 10 0.90625 9.53125 1.21875 9.25L8.4375 2H2.75C2.3125 2 2 1.6875 2 1.25C2 0.84375 2.3125 0.5 2.75 0.5H10.25C10.6562 0.5 11 0.84375 11 1.25Z"
            fill="black" />
        </svg>
        <div class="bd-setup-notice__text-wrap">
          <h3><?php esc_html_e('Quickstart', 'breakdance'); ?></h3>
          <span class="bd-setup-notice__card-tag"><?php esc_html_e('BETA', 'breakdance'); ?></span>
        </div>

        <div class="bd-setup-notice__card-text">
          <?php esc_html_e('Our quickstart guide will help you get started with Breakdance.', 'breakdance'); ?>
        </div>

        <div class="bd-setup-notice__card-button-wrapper">
          <a href="<?= home_url("/?breakdance=onboarding-app") ?>"
            class="bd-setup-notice__card-button bd-setup-notice__card-button-primary">
            <?php esc_html_e('Get Started', 'breakdance'); ?>
          </a>
        </div>
      </div>
      <?php } ?>

      <div class="bd-setup-notice__card">
        <svg
          width="11"
          height="11"
          viewBox="0 0 11 11"
          fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path
            d="M11 1.25V8.75C11 9.1875 10.6562 9.5 10.25 9.5C9.8125 9.5 9.5 9.1875 9.5 8.75V3.0625L2.28125 10.2812C2.125 10.4375 1.9375 10.5 1.75 10.5C1.53125 10.5 1.34375 10.4375 1.21875 10.2812C0.90625 10 0.90625 9.53125 1.21875 9.25L8.4375 2H2.75C2.3125 2 2 1.6875 2 1.25C2 0.84375 2.3125 0.5 2.75 0.5H10.25C10.6562 0.5 11 0.84375 11 1.25Z"
            fill="black" />
        </svg>

        <h3><?php esc_html_e('Learn Breakdance', 'breakdance'); ?></h3>
        <div class="bd-setup-notice__card-text">
          <?php esc_html_e('Our tutorials are designed to be convenient and straightforward.', 'breakdance'); ?>
        </div>

        <div class="bd-setup-notice__card-button-wrapper">
          <a href="https://breakdance.com/learn/" target="_blank"
            class="bd-setup-notice__card-button bd-setup-notice__card-button-secondary">
            <?php esc_html_e('See Tutorials', 'breakdance'); ?>
          </a>
        </div>
      </div>

      <div class="bd-setup-notice__card">
        <svg
          width="11"
          height="11"
          viewBox="0 0 11 11"
          fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path
            d="M11 1.25V8.75C11 9.1875 10.6562 9.5 10.25 9.5C9.8125 9.5 9.5 9.1875 9.5 8.75V3.0625L2.28125 10.2812C2.125 10.4375 1.9375 10.5 1.75 10.5C1.53125 10.5 1.34375 10.4375 1.21875 10.2812C0.90625 10 0.90625 9.53125 1.21875 9.25L8.4375 2H2.75C2.3125 2 2 1.6875 2 1.25C2 0.84375 2.3125 0.5 2.75 0.5H10.25C10.6562 0.5 11 0.84375 11 1.25Z"
            fill="black" />
        </svg>

        <h3><?php esc_html_e('Join Facebook group', 'breakdance'); ?></h3>
        <div class="bd-setup-notice__card-text">
          <?php esc_html_e('Our Facebook group has over 5000 happy and helpful members.', 'breakdance'); ?>
        </div>

        <div class="bd-setup-notice__card-button-wrapper">
          <a href="https://www.facebook.com/groups/breakdanceofficial" target="_blank"
            class="bd-setup-notice__card-button bd-setup-notice__card-button-secondary">
            <?php esc_html_e('Join Group', 'breakdance'); ?>
          </a>
        </div>
      </div>

      <div class="bd-setup-notice__card">
        <svg
          width="11"
          height="11"
          viewBox="0 0 11 11"
          fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path
            d="M11 1.25V8.75C11 9.1875 10.6562 9.5 10.25 9.5C9.8125 9.5 9.5 9.1875 9.5 8.75V3.0625L2.28125 10.2812C2.125 10.4375 1.9375 10.5 1.75 10.5C1.53125 10.5 1.34375 10.4375 1.21875 10.2812C0.90625 10 0.90625 9.53125 1.21875 9.25L8.4375 2H2.75C2.3125 2 2 1.6875 2 1.25C2 0.84375 2.3125 0.5 2.75 0.5H10.25C10.6562 0.5 11 0.84375 11 1.25Z"
            fill="black" />
        </svg>

        <h3><?php esc_html_e('Subscribe to YouTube', 'breakdance'); ?></h3>
        <div class="bd-setup-notice__card-text">
          <?php esc_html_e('We are constantly releasing new tutorials on our YouTube channel.', 'breakdance'); ?>
        </div>

        <div class="bd-setup-notice__card-button-wrapper">
          <a href="https://www.youtube.com/@OfficialBreakdance?sub_confirmation=1" target="_blank"
            class="bd-setup-notice__card-button bd-setup-notice__card-button-secondary">
            <?php esc_html_e('Subscribe', 'breakdance'); ?>
          </a>
        </div>
      </div>
    </div>

    <?php
    if ($includeFooter) {
    ?>
      <div class="bd-setup-notice__bottom">

        <svg 
          width="16"
          height="16"
          viewBox="0 0 512 512"
          fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path 
            d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"
            fill="#A3A3A3" />
        </svg>

        <a
          class="bd-setup-notice__bottom-text"
          href="https://x.com/TeamBreakdance"
          target="_blank">
          <?php esc_html_e('We post frequent updates on X', 'breakdance'); ?>
        </a>
      </div>
    <?php } ?>
  </div>
<?php
}
