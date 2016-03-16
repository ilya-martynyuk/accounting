(function(){
    'use strict';

    angular
        .module('app.loginPage')
        .config([
            '$stateProvider',
            'appConstants',
            moduleConfig
        ]);

    function moduleConfig($stateProvider, appConstants) {
        $stateProvider
            .state('auth.login', {
                'url': '/login',
                'templateUrl': appConstants.jsUrl + '/pageLogin/pageLogin.html',
                'controller': 'LoginPageController as lgPgCtrl'
            });
    }
})();