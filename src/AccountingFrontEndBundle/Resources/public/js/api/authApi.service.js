(function(){
    'use strict';

    angular
        .module('app.api')
        .service('authApiService', authApiService);

    authApiService.$inject = [
        '$http',
        '$httpParamSerializer',
        'appConstants'
    ];

    function authApiService($http, $httpParamSerializer, appConstants) {
        this.login = function(userLogin, userPassword) {
            return $http({
                'method': 'POST',
                'url': appConstants.apiUrl + 'auth/login',
                'data': $httpParamSerializer( {
                    'username': userLogin,
                    'password': userPassword
                })
            });
        }
    }
})();