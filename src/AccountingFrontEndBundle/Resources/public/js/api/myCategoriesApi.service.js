(function(){
    'use strict';

    angular
        .module('app.api')
        .service('myCategoriesApiService', myCategoriesApiService);

    myCategoriesApiService.$inject = [
        '$http',
        '$httpParamSerializer',
        'appConstants'
    ];

    function myCategoriesApiService($http, $httpParamSerializer, appConstants) {
        this.getSingle = function(categoryId) {
            return $http({
                'method': 'GET',
                'url': appConstants.apiUrl + 'users/me/categories/' + categoryId
            });
        };

        this.getAll = function() {
            return $http({
                'method': 'GET',
                'url': appConstants.apiUrl + 'users/me/categories'
            });
        };
    }
})();