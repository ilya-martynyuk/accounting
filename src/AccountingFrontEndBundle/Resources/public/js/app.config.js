(function(){
    'use strict';

    angular
        .module('app')
        .config([
            '$locationProvider',
            '$urlRouterProvider',
            '$httpProvider',
            'localStorageServiceProvider',
            moduleConfig
        ]);

    /**
     * @ngdoc function
     *
     * @description
     * Common application configuration file
     *
     * @param $locationProvider
     * @param $urlRouterProvider
     * @param $httpProvider
     * @param localStorageServiceProvider
     */
    function moduleConfig($locationProvider, $urlRouterProvider, $httpProvider, localStorageServiceProvider) {
        $httpProvider
            .defaults
            .headers
            .post['Content-Type'] =  'application/x-www-form-urlencoded';

        $httpProvider
            .interceptors
            .push('requestsInterceptorFactory');
        
        localStorageServiceProvider
            .setPrefix('accountingApp')
            .setNotify(true, true);

        $urlRouterProvider
            .otherwise("/login");
    }
})();