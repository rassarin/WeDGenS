/**
 * @fileOverview Gruntビルド設定
 */

/* -------------------------------------------------------------------------- */

module.exports = function (grunt) {

    'use strict';

    [
        'grunt-contrib-jst',
        'grunt-contrib-watch'
    ].forEach(function (name) {
         grunt.loadNpmTasks(name);
    });

    grunt.initConfig({
        watch: {
            jst: {
                files: ['templates/**/*.html'],
                tasks: ['jst:dev']
            }
        },

        jst: {
            options: {
                processName: function (filename) {
                    return filename.match(/templates\/(.+)\.html$/)[1];
                },
                processContent: function (src) {
                    return src.replace(/(^\s+|\s+$)/gm, '');
                },
                amd: true
            },
            dev: {
                files: {
                    '../../../public/js/app/templates/jst.js': ['templates/**/*.html']
                }
            }
        }
    });

    grunt.registerTask('default', ['watch']);
    grunt.registerTask('build', ['jst']);
};
