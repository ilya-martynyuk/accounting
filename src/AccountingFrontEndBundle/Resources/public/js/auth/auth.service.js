(function(){
    'use strict';

    angular
        .module('app.auth')
        .service('authService', authService);

    authService.$inject = [
        'usersApiService',
        'authApiService',
        'localStorageService',
        '$q'
    ];

    function authService(usersApiService, authApiService, localStorageService, $q) {
        var self = this,
            /**
             * Authorised user auth token
             *
             * @type {boolean}
             * @private
             */
            _authToken = false,

            /**
             * Authorised user profile information
             *
             * @type {{}}
             * @private
             */
            _userInfo = {},

            /**
             * This flag means that service did all initialization jobs (for example checking existence of auth token)
             * and ready to use
             *
             * @type {boolean}
             * @private
             */
            _serviceIsReady = false;

        /**
         * @ngdoc method
         * @name authService#isAuthorized
         *
         * @description
         * Checks whether user is authorized
         *
         * @returns {boolean}
         */
        self.isAuthorized = function() {
            return _authToken !== false;
        };

        /**
         * @ngdoc method
         * @name authService#getAuthToken
         *
         * @description
         * Returns authorised user auth token
         *
         * @returns {string}
         */
        self.getAuthToken = function() {
            return _authToken;
        };

        /**
         * @ngdoc method
         * @name authService#getAuthToken
         *
         * @description
         * Returns authorised user auth token
         *
         * @returns {object}
         */
        self.getUserInfo = function() {
            return _userInfo;
        };

        /**
         * @ngdoc method
         * @name authService#getAuthToken
         *
         * @description
         * Checking whether service ready to use. Service ready to use after checking auth token (if it exist).
         *
         * @returns {boolean}
         */
        self.isServiceReady = function() {
            return _serviceIsReady;
        };

        self.login = function(username, password){
            var defer = $q.defer();

            authApiService
                .login(username, password)
                .success(function(response) {
                    _authToken = response.access_token;

                    localStorageService.set('auth_token', _authToken);

                    defer.resolve( response );
                })
                .error(function(response, status) {
                    defer.reject( response );
                });

            return defer.promise
        };

        self.init = function() {
            _authToken = localStorageService.get('auth_token');

            if (_authToken) {
                // Trying to get user info using found auth token
                usersApiService
                    .me(_authToken)
                    .success(function(response) {
                        _userInfo = response.data;
                        _serviceIsReady = true;
                    })
                    .error(function(response, status) {
                        if (status === 403) {
                            localStorageService.remove('auth_token');
                        }

                        _serviceIsReady = true;
                    });
            } else {
                _serviceIsReady = true;
            }
        };
    }
})();