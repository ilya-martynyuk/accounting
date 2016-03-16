(function(){
    'use strict';

    angular
        .module('app.api')
        .service('myOperationsApiService', myOperationsApiService);

    myOperationsApiService.$inject = [
        '$http',
        '$httpParamSerializer',
        'appConstants'
    ];

    function myOperationsApiService($http, $httpParamSerializer, appConstants) {
        this.getOperations = function(purseId) {
            if (purseId) {
                return $http({
                    'method': 'GET',
                    'url': appConstants.apiUrl + 'users/me/purses/' + purseId + '/operations'
                });
            } else {
                return $http({
                    'method': 'GET',
                    'url': appConstants.apiUrl + 'users/me/operations'
                });
            }
        };
    }
})();