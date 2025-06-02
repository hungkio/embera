import tinyMCE from 'tinymce/tinymce';
import 'tinymce/themes/modern/theme';
import 'tinymce/themes/mobile/theme';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/paste';
import 'tinymce/plugins/autosave';
import 'tinymce/plugins/table';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/wordcount';
import 'tinymce/plugins/code';
import 'tinymce/plugins/image'
import 'tinymce/plugins/fullscreen'
import 'tinymce/plugins/preview'

let isRTL = $('body').hasClass('rtl');

let direction = (isRTL) ? 'rtl' : 'ltr';

tinyMCE.baseURL = `${Config.baseUrl}/backend/js/wysiwyg`;

let langURl = `${Config.baseUrl}/backend/js/vi_VN.js`;

tinyMCE.init({
    init_instance_callback: function (editor) {
        editor.on('Change', function (e) {
            tinyMCE.triggerSave();
            $('#'+editor.id).valid();
        });
    },

    images_upload_handler: function (blobInfo, success, failure) {
        var xhr, formData;

        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', '/admin/uploads-tinymce');

        xhr.onload = function() {
            var json;

            if (xhr.status != 200) {
                failure('HTTP Error: ' + xhr.status);
                return;
            }

            json = JSON.parse(xhr.responseText);

            if (!json || typeof json.location != 'string') {
                failure('Invalid JSON: ' + xhr.responseText);
                return;
            }

            success(json.location);
        };

        formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        formData.append('_token', window.Config.csrf)

        xhr.send(formData);
    },
    automatic_uploads: true,
    paste_data_images: true,
    selector: '.wysiwyg',
    theme: 'modern',
    mobile: { theme: 'mobile' },
    height: 300,
    menubar: false,
    branding: false,
    image_advtab: true,
    image_title: true,
    relative_urls: false,
    directionality: direction,
    cache_suffix: `?v=1`,
    plugins: 'lists, link, table, paste, autosave, autolink, wordcount, code, image, fullscreen, preview',
    toolbar: 'styleselect bold italic underline | bullist numlist | alignleft aligncenter alignright alignjustify | outdent indent | link table code image fullscreen preview',
    language_url : langURl
});
