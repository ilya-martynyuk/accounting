(function(){
    'use strict';

    /**
     * @ngdoc module
     * @name app
     *
     * @requires ui.router
     *
     * @description
     * Common application module
     */
    angular
        .module('app', [
            'LocalStorageModule',
            'app.common',
            'app.api',
            'app.loginPage',
            'app.accountPage'
        ]);
})();