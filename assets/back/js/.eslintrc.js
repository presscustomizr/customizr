module.exports = {
    //https://eslint.org/docs/user-guide/configuring#configuration-cascading-and-hierarchy
    "globals": {
        // localized params
        "SliderAjax":true,
        "ajaxurl" : true//global var declared by WP when is_admin() => ajaxurl = '/wp-admin/admin-ajax.php',
    }
};