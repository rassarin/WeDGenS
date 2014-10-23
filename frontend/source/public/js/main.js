/**
 * @fileOverview エントリーポイント
 */

/* -------------------------------------------------------------------------- */

require([
    'jquery',
    'backbone',
    'app/views/AppView',
    'app/collections/Libraries',
    'app/routers/Router',
],
function ($, Backbone, AppView, Libraries, Router) {

    'use strict';

    var appRouter = new Router();
    var appView   = new AppView({
        'router': appRouter
    });

    $(function () {
        $('#content').append(appView.render().el);
        Backbone.history.start({root: wgs.constant.uiBaseUrl});
    });
});
