<?php

namespace Breakdance\Admin;

use function Breakdance\SetupWizard\Onboarding\showNotice;

function breakdanceHomePage()
{
?>
    <style>
        #wpcontent {
            padding: 0;
        }

        .breakdance-home__footer {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 14px;
        }

        .breakdance-home__footer {
            color: #D4D4D4;
        }

        .breakdance-home__footer svg path {
            fill: hsl(0deg 0% 80%);
        }

        .breakdance-home__social {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .breakdance-home__video {
            position: relative;
            padding-bottom: 56.25%;
        }

        .breakdance-home__video iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .bd-setup-notice--video {
            margin-top: 24px;
        }
    </style>
    <div class='breakdance-home'>

        <?php showNotice(false, false); ?>

        <div class="bd-setup-notice bd-setup-notice--video notice">
            <div class="breakdance-home__video">
                <iframe src="https://www.youtube.com/embed/wrpcUw6KIEQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
        </div>

        <div class="breakdance-home__footer">
            <div class="breakdance-home__social">
                <a href="https://www.facebook.com/groups/breakdanceofficial" target="_blank">
                    <svg width="8" height="16" viewBox="0 0 8 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 5.33333H0V8H2V16H5.33333V8H7.76133L8 5.33333H5.33333V4.222C5.33333 3.58533 5.46133 3.33333 6.07667 3.33333H8V0H5.46133C3.064 0 2 1.05533 2 3.07667V5.33333Z" fill="#E5E5E5" />
                    </svg>
                </a>
                
                <a href="https://x.com/TeamBreakdance" target="_blank">
                    <svg width="16" height="16" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z" fill="#E5E5E5" />
                    </svg>
                </a>

                <a href="https://www.youtube.com/@OfficialBreakdance" target="_blank">
                    <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.0767 0.122689C10.674 -0.0413109 5.32267 -0.0406442 2.92333 0.122689C0.325333 0.300022 0.0193333 1.86936 0 6.00002C0.0193333 10.1234 0.322667 11.6994 2.92333 11.8774C5.32333 12.0407 10.674 12.0414 13.0767 11.8774C15.6747 11.7 15.9807 10.1307 16 6.00002C15.9807 1.87669 15.6773 0.300689 13.0767 0.122689ZM6 8.66669V3.33336L11.3333 5.99536L6 8.66669Z" fill="#E5E5E5" />
                    </svg>
                </a>
            </div>

            <div class="breakdance-home__copyright">Copyright &copy; Soflyy. All Rights Reserved.</div>
        </div>
    </div>
<?php
}



function oxygenHomePage()
{
?>
    <style>
    </style>
    <div class='oxygen-home wrap'>

        <h1><?php esc_html_e('Oxygen Beta', 'breakdance'); ?></h1>

        <p><?php esc_html_e('Thank you for beta testing the new Oxygen.', 'breakdance'); ?></p>

        <p>
            <?php esc_html_e('-The Oxygen Team', 'breakdance'); ?>
        </p>

    </div>
<?php
}
