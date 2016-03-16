;(function(){
    'use strict';

    angular
        .module('app.common')
        .factory('requestsInterceptorFactory', requestsInterceptorFactory);

    requestsInterceptorFactory.$inject = [
        'appConstants',
        '$injector'
    ];

    function requestsInterceptorFactory(appConstants, $injector) {
        return {
            request: function(config) {
                var authService = $injector.get('authService');

                // Skipping the .html requests (our views).
                if (/.*\.html$/.test( config.url )) {
                    return config;
                }

                if (authService.isAuthorized()) {
                    config.headers['X_BEARER_TOKEN'] = authService.getAuthToken();
                }

                return config;
            },
            responseErro: function( rejection ) {
                //TOD: here will be a code which intercepts API errors.
            }
        };
    }
})();