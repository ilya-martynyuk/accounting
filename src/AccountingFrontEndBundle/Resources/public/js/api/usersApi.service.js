(function(){
    'use strict';

    angular
        .module('app.api')
        .service('usersApiService', usersApiService);

    usersApiService.$inject = [
        '$http',
        '$httpParamSerializer',
        'appConstants'
    ];

    function usersApiService($http, $httpParamSerializer, appConstants) {
        this.me = function(authToken) {
            return $http({
                'method': 'GET',
                'url': appConstants.apiUrl + 'users/me',
                'data': $httpParamSerializer( {
                    'access_token': authToken
                })
            });
        };
    }
})();