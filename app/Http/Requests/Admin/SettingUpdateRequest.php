<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'store_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg','max:2048'],
            'store_favicon' => ['nullable', 'image', 'mimes:jpeg,png,jpg','max:2048'],
            'store_name' => ['required','nullable', 'string', 'max:255'],
            'store_description' => ['required','nullable', 'string'],
            'store_slogan' => ['required','nullable', 'string'],
            'store_phone' => ['required', 'numeric', 'digits_between:10,11'],
            'store_email' => ['required', 'email', 'max:255'],
            'store_address' => ['required', 'string', 'max:255'],
            'post_taxonomy' => ['nullable'],
            'menu_header' => ['nullable'],
            'menu_footer_1' => ['nullable'],
            'menu_footer_2' => ['nullable'],
            'link_facebook' => ['nullable', 'url', 'max:255'],
            'link_google' => ['nullable', 'url', 'max:255'],
            'link_twitter' => ['nullable', 'url', 'max:255'],
            'link_instagram' => ['nullable', 'url', 'max:255'],
            'link_youtube' => ['nullable', 'url', 'max:255'],
            'link_zalo' => ['nullable', 'url', 'max:255'],
            'mail_from_address' => ['nullable', 'string'],
            'mail_from_name' => ['nullable', 'string'],
            'mail_host' => ['nullable', 'string'],
            'mail_port' => ['nullable', 'string'],
            'mail_username' => ['nullable', 'string'],
            'mail_password' => ['nullable', 'string'],
            'mail_encryption' => ['nullable', 'string'],
            'store_banner' => ['nullable'],
            'language' => ['nullable'],
            'date_format' => ['required'],
            'custom_styles' => ['nullable'],
            'custom_scripts' => ['nullable'],
            'store_taxon_level' => ['required', 'numeric', 'min:1'],
            'store_menu_level' => ['required', 'numeric', 'min:1'],
            'analytics' => ['nullable'],
        ];
    }

    public function attributes()
    {
        return [
            'store_logo' => 'Logo',
            'store_favicon' => 'Favicon',
            'store_name' => 'tên cửa hàng',
            'store_description' => 'mô tả cửa hàng',
            'store_slogan' => 'khẩu hiệu',
            'store_phone' => 'Hotline liên hệ',
            'store_email' => 'Email liên hệ',
            'store_address' => 'Địa chỉ liên hệ',
            'post_taxonomy' => 'danh mục bài viết',
            'menu_header' => 'menu đầu trang',
            'menu_footer_1' => 'menu cuối trang 1',
            'menu_footer_2' => 'menu cuối trang 2',
            'link_facebook' => 'đường dẫn facebook',
            'link_google' => 'đường dẫn google',
            'link_twitter' => 'đường dẫn twitter',
            'link_instagram' => 'đường dẫn instagram',
            'link_youtube' => 'đường dẫn youtube',
            'link_zalo' => 'đường dẫn zalo',
            'mail_from_address' => 'địa chỉ gửi email',
            'mail_from_name' => 'tên người gửi email',
            'mail_host' => 'máy chủ mail',
            'mail_port' => 'cổng chủ mail',
            'mail_username' => 'tài khoản mail',
            'mail_password' => 'Mật khẩu mail',
            'mail_encryption' => 'Mã hóa Mail',
            'store_banner' => 'banner',
            'language' => 'ngôn ngữ',
            'date_format' => 'Định dạng thời gian',
            'custom_styles' => 'tùy chỉnh giao diện',
            'custom_scripts' => 'tùy chỉnh script',
            'store_taxon_level' => 'tùy chỉnh số cấp danh mục',
            'store_menu_level' => 'tùy chỉnh số cấp menu',
        ];
    }

    public function messages()
    {
        return [
            'store_phone.numeric' => 'Số điện thoại không hợp lệ',
            'store_phone.max' => 'Số điện thoại không hợp lệ',
            'store_email.email' => 'Nhập email hợp lệ',
            'store_email.regex' => 'Nhập email hợp lệ',
        ];
    }
}
