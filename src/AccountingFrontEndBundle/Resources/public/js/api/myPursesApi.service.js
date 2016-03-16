(function(){
    'use strict';

    angular
        .module('app.api')
        .service('myPursesApiService', myPursesApiService);

    myPursesApiService.$inject = [
        '$http',
        '$httpParamSerializer',
        'appConstants'
    ];

    function myPursesApiService($http, $httpParamSerializer, appConstants) {
        this.getPurses = function(purseId) {
            if (purseId) {
                return $http({
                    'method': 'GET',
                    'url': appConstants.apiUrl + 'users/me/purses/' + purseId
                });
            } else {
                return $http({
                    'method': 'GET',
                    'url': appConstants.apiUrl + 'users/me/purses'
                });
            }
        };
    }
})();