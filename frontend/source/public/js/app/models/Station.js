/**
 * @fileOverview ステーションモデルクラス定義モジュール
 */

/* -------------------------------------------------------------------------- */

/**
 * ステーションモデルクラス
 */
define([
    'underscore',
    'backbone',
    './AbstractModel'
],
function (_, Backbone, AbstractModel) {

    'use strict';

    return AbstractModel.extend({
        idAttribute: "station_id",
        defaults: function() {
            return {
                "station_id": null,
                "station_name": null,
                "place_alt": null,
                "place_lat": null,
                "place_lon": null,
                "start": null,
                "end": null,
                "elements": null
            };
        }
    });
});
