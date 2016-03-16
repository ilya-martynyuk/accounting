(function(){
    'use strict';

    angular
        .module('app.auth')
        .config([
            '$stateProvider',
            'appConstants',
            moduleConfig
        ]);

    function moduleConfig($stateProvider, appConstants) {
        $stateProvider
            .state('auth', {
                abstract: true,
                template: '<ui-view/>'
            });
    }
})();