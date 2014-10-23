/**
 * @fileOverview RequireJS設定
 */

/* -------------------------------------------------------------------------- */

var require = {
    baseUrl: wgs.constant.jsBaseUrl,
    paths: {
        'jquery'          : 'vendor/jquery-1.10.2.min',
        'jquery.ui'       : 'vendor/jquery-ui-1.10.4.custom.min',
        'jquery.validate' : 'vendor/jquery.validate',
        'underscore'      : 'vendor/underscore-min',
        'backbone'        : 'vendor/backbone-min',
        'text'            : 'vendor/text-2.0.10',
    },
    shim: {
        'underscore': {
            exports: '_'
        },
        'backbone': {
            exports: 'Backbone',
            deps: ['jquery', 'underscore']
        },
        'jquery.ui': {
            deps: ['jquery']
        },
        'jquery.validate': {
            deps: ['jquery']
        }
    }
};
