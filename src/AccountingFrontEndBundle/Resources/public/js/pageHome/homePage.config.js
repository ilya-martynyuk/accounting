(function(){
    'use strict';

    angular
        .module('app.accountPage')
        .config([
            '$stateProvider',
            'appConstants',
            moduleConfig
        ]);

    function moduleConfig($stateProvider, appConstants) {
        $stateProvider
            .state('home', {
                url: '',
                views: {
                    '': {
                        'templateUrl': appConstants.jsUrl + '/common/views/account.html',
                        'controller': 'AccountController as acCtrl'
                    },
                    'main-content@home': {
                        'templateUrl': appConstants.jsUrl + '/pageHome/homePage.html',
                        'controller': 'HomePageController as hmPgCtrl'
                    }
                }
            });
    }
})();