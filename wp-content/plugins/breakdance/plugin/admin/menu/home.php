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
        .oxygen-home {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 24px 40px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #1d2327;
        }

        .oxygen-home__hero {
            padding: 32px 0 24px;
        }

        .oxygen-home__logo {
            display: block;
            margin-bottom: 20px;
        }

        .oxygen-home__tagline {
            font-size: 14px;
            color: #646970;
            margin: 0;
        }

        .oxygen-home__links {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 16px 0;
            border-top: 1px solid #dcdcde;
            border-bottom: 1px solid #dcdcde;
        }

        .oxygen-home__pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border: 1px solid #c3c4c7;
            border-radius: 100px;
            color: #2271b1;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            background: #fff;
            transition: background 0.12s, border-color 0.12s, color 0.12s;
        }

        .oxygen-home__pill:hover {
            background: #f0f6fc;
            border-color: #2271b1;
            color: #135e96;
        }

        .oxygen-home__video-wrap {
            margin: 28px 0 0;
            position: relative;
            padding-bottom: 56.25%;
            background: #000;
            border-radius: 6px;
            overflow: hidden;
        }

        .oxygen-home__video-facade {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .oxygen-home__play-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: transform 0.15s;
        }

        .oxygen-home__play-btn:hover {
            transform: scale(1.1);
        }

        .oxygen-home__video-wrap iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .oxygen-home__footer {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: #646970;
        }

        .oxygen-home__social {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .oxygen-home__social a {
            display: flex;
            opacity: 0.5;
            transition: opacity 0.15s;
        }

        .oxygen-home__social a:hover {
            opacity: 1;
        }
    </style>
    <div class="oxygen-home">

        <div class="oxygen-home__hero">
            <svg class="oxygen-home__logo" xmlns="http://www.w3.org/2000/svg" width="200" height="33" viewBox="0 0 400 66" fill="#000" aria-label="Oxygen"><path fill-rule="evenodd" clip-rule="evenodd" d="M33 57.75C34.6969 57.75 36.354 57.5792 37.9549 57.2539C37.4093 55.7914 37.1111 54.2083 37.1111 52.5556C37.1111 45.1304 43.1304 39.1111 50.5556 39.1111C52.7224 39.1111 54.7696 39.6237 56.5823 40.5343C57.3407 38.1587 57.75 35.6271 57.75 33C57.75 19.331 46.669 8.25 33 8.25C19.331 8.25 8.25 19.331 8.25 33C8.25 46.669 19.331 57.75 33 57.75ZM62.8459 47.0974C64.8686 42.8225 66 38.0434 66 33C66 14.7746 51.2254 0 33 0C14.7746 0 0 14.7746 0 33C0 51.2254 14.7746 66 33 66C36.7864 66 40.424 65.3623 43.811 64.1884C45.7935 65.3403 48.0975 66 50.5556 66C57.9807 66 64 59.9807 64 52.5556C64 50.6123 63.5877 48.7654 62.8459 47.0974ZM56.7812 52.5938C56.7812 56.011 54.011 58.7812 50.5938 58.7812C47.1765 58.7812 44.4062 56.011 44.4062 52.5938C44.4062 49.1765 47.1765 46.4062 50.5938 46.4062C54.011 46.4062 56.7812 49.1765 56.7812 52.5938Z"/><path d="M350.144 63.9297V2.02734H358.706L386.232 42.2681C386.917 43.267 387.688 44.5085 388.544 45.9926C389.4 47.4766 390.085 48.7181 390.599 49.717L391.412 51.2153H391.584C391.184 47.5337 390.984 44.5513 390.984 42.2681V2.02734H399.674V63.9297H391.155L363.543 23.7745C362.858 22.7471 362.088 21.4913 361.232 20.0073C360.375 18.4947 359.691 17.2389 359.177 16.2401L358.363 14.7417H358.192C358.592 18.4233 358.792 21.4343 358.792 23.7745V63.9297H350.144Z"/><path d="M289.365 63.9297V2.02734H325.282V9.56179H298.013V28.9544H320.231V36.4889H298.013V56.3952H326.781V63.9297H289.365Z"/><path d="M208.08 32.893C208.08 28.4693 208.879 24.3026 210.477 20.3926C212.104 16.4542 214.302 13.0722 217.07 10.2468C219.867 7.39287 223.22 5.13824 227.13 3.48294C231.069 1.82765 235.25 1 239.673 1C242.442 1 245.11 1.25686 247.679 1.77057C250.247 2.28428 252.359 2.91215 254.015 3.65418C255.698 4.36767 257.182 5.09543 258.467 5.83746C259.78 6.55095 260.736 7.16455 261.335 7.67826L262.191 8.44883L257.825 14.913C257.625 14.7418 257.354 14.5278 257.011 14.2709C256.669 13.9855 255.898 13.5003 254.699 12.8154C253.529 12.1304 252.302 11.5168 251.018 10.9746C249.762 10.4323 248.164 9.94716 246.223 9.51906C244.283 9.09097 242.328 8.87692 240.358 8.87692C235.649 8.87692 231.497 9.94716 227.901 12.0876C224.333 14.2281 221.622 17.0963 219.767 20.6923C217.94 24.2883 217.027 28.2981 217.027 32.7217C217.027 37.4308 218.026 41.6404 220.024 45.3505C222.022 49.0606 224.747 51.9289 228.2 53.9552C231.682 55.9815 235.564 56.9947 239.845 56.9947C241.842 56.9947 243.812 56.7378 245.752 56.2241C247.693 55.6818 249.334 55.0397 250.675 54.2977C252.017 53.5271 253.201 52.7708 254.229 52.0288C255.285 51.2582 256.069 50.6018 256.583 50.0595L257.354 49.289V40.3418H247.293V32.8074H265.273V63.9298H257.611V60.0769L257.739 57.4227H257.525C257.354 57.6225 257.083 57.8936 256.712 58.2361C256.369 58.5786 255.584 59.1922 254.357 60.0769C253.158 60.9331 251.874 61.7037 250.504 62.3886C249.163 63.045 247.393 63.6444 245.196 64.1866C243.027 64.7289 240.801 65 238.518 65C234.379 65 230.455 64.2152 226.745 62.6455C223.035 61.0473 219.796 58.864 217.027 56.0957C214.287 53.2988 212.104 49.8883 210.477 45.8642C208.879 41.8401 208.08 37.5164 208.08 32.893Z"/><path d="M164.767 63.9297V37.7304L143.962 2.02734H153.808L165.281 22.3618C165.852 23.3892 166.451 24.5593 167.079 25.8722C167.707 27.1564 168.192 28.1981 168.534 28.9972L169.048 30.1959H169.219C170.447 27.3134 171.702 24.702 172.987 22.3618L184.288 2.02734H194.134L173.458 37.7304V63.9297H164.767Z"/><path d="M81 63.9297L100.564 32.0367L82.2415 2.02734H92.2161L101.977 18.7658L105.915 26.129H106.086C107.256 23.5034 108.512 21.049 109.854 18.7658L119.571 2.02734H129.589L111.266 32.0367L130.787 63.9297H120.984L109.768 44.9223L105.829 37.816H105.658C104.545 40.2704 103.318 42.6392 101.977 44.9223L90.7177 63.9297H81Z"/></svg>
            <p class="oxygen-home__tagline"><?php esc_html_e('Pixel-perfect design meets WordPress power.', 'breakdance'); ?></p>
        </div>

        <nav class="oxygen-home__links">
            <a class="oxygen-home__pill" href="https://oxygenbuilder.com/tutorials/" target="_blank"><?php esc_html_e('Tutorials', 'breakdance'); ?></a>
            <a class="oxygen-home__pill" href="https://oxygenbuilder.com/documentation/" target="_blank"><?php esc_html_e('Documentation', 'breakdance'); ?></a>
            <a class="oxygen-home__pill" href="https://oxygenbuilder.com/category/releases/" target="_blank"><?php esc_html_e('Releases', 'breakdance'); ?></a>
            <a class="oxygen-home__pill" href="https://oxygenbuilder.com/support/" target="_blank"><?php esc_html_e('Support', 'breakdance'); ?></a>
            <a class="oxygen-home__pill" href="https://www.facebook.com/groups/1626639680763454/" target="_blank"><?php esc_html_e('Facebook Community', 'breakdance'); ?></a>
        </nav>

        <div class="oxygen-home__video-wrap">
            <div class="oxygen-home__video-facade" id="oxygen-video-facade"
                 style="background-image: url('https://img.youtube.com/vi/SYHcevTZ_a0/maxresdefault.jpg')">
                <button class="oxygen-home__play-btn" aria-label="<?php esc_attr_e('Play video', 'breakdance'); ?>">
                    <svg width="64" height="44" viewBox="0 0 68 48" xmlns="http://www.w3.org/2000/svg">
                        <path d="M66.52 7.74A8.54 8.54 0 0 0 60.71 1.9C55.4 0 34 0 34 0S12.6 0 7.29 1.9A8.54 8.54 0 0 0 1.48 7.74C0 13.15 0 24 0 24s0 10.85 1.48 16.26a8.54 8.54 0 0 0 5.81 5.84C12.6 48 34 48 34 48s21.4 0 26.71-1.9a8.54 8.54 0 0 0 5.81-5.84C68 34.85 68 24 68 24s0-10.85-1.48-16.26z" fill="red"/>
                        <path d="M27 34l18-10-18-10z" fill="#fff"/>
                    </svg>
                </button>
            </div>
            <script>
            document.getElementById('oxygen-video-facade').addEventListener('click', function() {
                var iframe = document.createElement('iframe');
                iframe.src = 'https://www.youtube-nocookie.com/embed/SYHcevTZ_a0?autoplay=1';
                iframe.title = '<?php echo esc_js(__('Oxygen overview', 'breakdance')); ?>';
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
                iframe.allowFullscreen = true;
                this.replaceWith(iframe);
            });
            </script>
        </div>

        <footer class="oxygen-home__footer">
            <div class="oxygen-home__social">
                <a href="https://www.youtube.com/oxygen-builder" target="_blank" aria-label="YouTube">
                    <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.0767 0.122689C10.674 -0.0413109 5.32267 -0.0406442 2.92333 0.122689C0.325333 0.300022 0.0193333 1.86936 0 6.00002C0.0193333 10.1234 0.322667 11.6994 2.92333 11.8774C5.32333 12.0407 10.674 12.0414 13.0767 11.8774C15.6747 11.7 15.9807 10.1307 16 6.00002C15.9807 1.87669 15.6773 0.300689 13.0767 0.122689ZM6 8.66669V3.33336L11.3333 5.99536L6 8.66669Z" fill="#1d2327"/>
                    </svg>
                </a>
                <a href="https://www.facebook.com/groups/1626639680763454/" target="_blank" aria-label="Facebook">
                    <svg width="8" height="16" viewBox="0 0 8 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 5.33333H0V8H2V16H5.33333V8H7.76133L8 5.33333H5.33333V4.222C5.33333 3.58533 5.46133 3.33333 6.07667 3.33333H8V0H5.46133C3.064 0 2 1.05533 2 3.07667V5.33333Z" fill="#1d2327"/>
                    </svg>
                </a>
            </div>
            <span><?php esc_html_e('Copyright &copy; Soflyy. All Rights Reserved.', 'breakdance'); ?></span>
        </footer>

    </div>
<?php
}
