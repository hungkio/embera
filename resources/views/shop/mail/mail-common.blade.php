<style>
    html,
    body {
        margin: 0 auto !important;
        padding: 0 !important;
        height: 100% !important;
        width: 100% !important;
        background: #f1f1f1 !important;
    }

    /* What it does: Stops email clients resizing small text. */
    * {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
    }

    /* What it does: Centers email on Android 4.4 */
    div[style*="margin: 16px 0"] {
        margin: 0 !important;
    }

    /* What it does: Stops Outlook from adding extra spacing to tables. */
    table,
    td {
        mso-table-lspace: 0pt !important;
        mso-table-rspace: 0pt !important;
    }

    /* What it does: Fixes webkit padding issue. */
    table {
        border-spacing: 0 !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
        margin: 0 auto !important;
    }

    /* What it does: Uses a better rendering method when resizing images in IE. */
    img {
        -ms-interpolation-mode: bicubic;
    }

    /* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
    a {
        text-decoration: none;
    }

    /* What it does: A work-around for email clients meddling in triggered links. */
    *[x-apple-data-detectors], /* iOS */
    .unstyle-auto-detected-links *,
    .aBn {
        border-bottom: 0 !important;
        cursor: default !important;
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
    }

    /* If the above doesn't work, add a .g-img class to any image in question. */
    img.g-img + div {
        display: none !important;
    }

    /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
    /* Create one of these media queries for each additional viewport size you'd like to fix */

    /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
    @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
        u ~ div .email-container {
            min-width: 320px !important;
        }
    }

    /* iPhone 6, 6S, 7, 8, and X */
    @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
        u ~ div .email-container {
            min-width: 375px !important;
        }
    }

    /* iPhone 6+, 7+, and 8+ */
    @media only screen and (min-device-width: 414px) {
        u ~ div .email-container {
            min-width: 414px !important;
        }
    }

</style>
<style>
    .bg_white {
        background: #ffffff;
    }
    .bg_black {
        background: #000000;
    }
    .email-section {
        padding: 2.5em;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: 'Poppins', sans-serif;
        color: #000000;
        margin-top: 0;
    }

    body {
        font-family: 'Poppins', sans-serif;
        font-weight: 400;
        color: rgba(0, 0, 0, .4);
    }

    a {
        color: #0d0cb5;
    }

    table {
    }

    /*LOGO*/

    .logo h1 {
        margin: 0;
    }

    .logo h1 a {
        color: #000000;
        font-size: 18px;
        font-weight: 700;
        text-transform: uppercase;
        font-family: 'Poppins', sans-serif;
    }

    .navigation {
        padding: 0;
    }

    .navigation li {
        list-style: none;
        display: inline-block;;
        margin-left: 5px;
        font-size: 13px;
        font-weight: 500;
    }

    .navigation li a {
        color: rgba(0, 0, 0, .4);
    }


    .hero .icon a {
        display: block;
        width: 60px;
        margin: 0 auto;
    }

    .hero .text {
        color: rgba(255, 255, 255, .8);
    }

    .hero .text h2 {
        color: #ffffff;
        font-size: 30px;
        margin-bottom: 0;
    }

    .heading-section h2 {
        color: #000000;
        font-size: 20px;
        margin-top: 0;
        line-height: 1.4;
        font-weight: 700;
        text-transform: uppercase;
    }
    .heading-section-white h2 {
        font-family: line-height: 1;
        padding-bottom: 0;
    }

    .heading-section-white h2 {
        color: #ffffff;
    }

    .icon img {
    }

    .text-services h3 {
        font-size: 16px;
        font-weight: 600;
    }

    .services-list img {
        float: left;
    }

    .services-list .text {
        width: calc(100% - 60px);
        float: right;
    }

    .services-list h3 {
        margin-top: 0;
        margin-bottom: 0;
    }

    .services-list p {
        margin: 0;
    }

    .img .icon a {
        display: block;
        width: 60px;
        position: absolute;
        top: 0;
        left: 50%;
        margin-left: -25px;
    }

    /*FOOTER*/

    .footer {
        color: rgba(255, 255, 255, .5);

    }

    .footer .heading {
        color: #ffffff;
        font-size: 20px;
    }

    .footer ul {
        margin: 0;
        padding: 0;
    }

    .footer ul li {
        list-style: none;
        margin-bottom: 10px;
    }

    .footer ul li a {
        color: rgba(255, 255, 255, 1);
    }
</style>


<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #222222;">
<center style="width: 100%; background-color: #f1f1f1;">
    <div
        style="display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>
    <div style="max-width: 600px; margin: 0 auto; background: #fafafa;" class="email-container">
        <!-- BEGIN BODY -->

        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
               style="margin: auto;">
{{--            <tr>--}}
{{--                <td valign="top" class="bg_white" style="padding: 1em 2.5em;">--}}
{{--                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">--}}
{{--                        <tr>--}}
{{--                            <td width="40%" class="logo text-overflow-40" style="text-align: left; font-size: 16px;">--}}
{{--                                <h1><a href="{{ route('home') }}">{{ setting('store_name',  __('Trang chủ')) }}</a></h1>--}}
{{--                            </td>--}}
{{--                            <td width="60%" class="logo" style="text-align: right;">--}}
{{--                                <ul class="navigation">--}}
{{--                                    <li><a href="{{ route('page.contact') }}">{{ __('Liên hệ') }} </a></li>--}}
{{--                                </ul>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                    </table>--}}
{{--            </tr><!-- end tr -->--}}
            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                   style="margin: auto;">
                <tr>
                </tr><!-- end: tr -->
                @if(isset($body))
                    <div style="max-width: 800px; min-height: 352px; text-align: left!important;" class="email_body">
                        {!! $body !!}
                    </div>
                @else
                <div style="max-width: 800px; min-height: 352px;" class="email_body">
                    <div style="padding: 15px;">
                        {{ __('Đăng kí liên hệ mới') }}
                    </div>
                    <div style="padding: 15px;">
                        <p>{{ __('Họ tên') }}: <strong>[first_name] [last_name]</strong></p>
                        <p>{{ __('Email') }}: <strong>[email]</strong></p>
                        <p>{{ __('Số điện thoại') }}: <strong>[phone]</strong></p>
                        <p>{{ __('Tiêu đề') }}: <strong>[title]</strong></p>
                        <p>{{ __('Nội dung') }}: <strong>[message]</strong></p>
                    </div>

                </div>
                @endif
{{--                <tr>--}}
{{--                    <td valign="middle" class="bg_black footer email-section">--}}
{{--                        <table>--}}
{{--                            <tr>--}}
{{--                                <td valign="top" width="100%">--}}
{{--                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="text-overflow-full">--}}
{{--                                        <tr>--}}
{{--                                            <td style="text-align: left; padding-right: 10px; color: white">--}}
{{--                                                <p style="font-size: 15px; line-height: 18px;">{{ __('Bản quyển thuộc') }} &copy; {{ date('Y') }} <a href="{{ route('home') }}" style="color: #b9b9b9;">{{ setting('store_name', 'LAPO.VN') }} {{ setting('store_slogan', 'Dịch vụ Website uy tín') }}.</a> {{ __('Được phát triển và duy trì bởi') }} <a--}}
{{--                                                        href="https://lapo.vn" target="_blank" style="color: #b9b9b9;">LAPO.VN</a></p>--}}
{{--                                            </td>--}}
{{--                                        </tr>--}}
{{--                                    </table>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        </table>--}}
{{--                    </td>--}}
{{--                </tr>--}}
            </table>
        </table>
    </div>
</center>
</body>
