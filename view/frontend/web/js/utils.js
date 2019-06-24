define([
    'jquery',
    'ko'
], function ($, ko) {
    'use strict';

    return {

        /**
         * Check if a param GET exist on an url given
         * @param name
         * @param url
         * @returns {string}
         */
        getParameterByName: function (name, url) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            let regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(url);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
    }
});
