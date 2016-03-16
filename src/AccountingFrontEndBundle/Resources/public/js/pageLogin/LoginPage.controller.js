(function(){
    'use strict';

    angular
        .module('app.loginPage')
        .controller('LoginPageController', loginPageController);

    loginPageController.$inject = [
        'authService'
    ];

    function loginPageController(authService) {
        var self = this;

        self.credentials = {
            username: '',
            password: ''
        };

        self.loginInProgress = false;

        self.errors = {};

        self.doLogin = function() {
            self.loginInProgress = true;
            self.errors = {};

            authService
                .login(self.credentials.username, self.credentials.password)
                .then(function(response){
                    self.errors = {};
                }, function(response) {
                    if (response.code !== 401) {
                        return;
                    }

                    self.errors = response.reason;
                })
                .finally(function(){
                    self.loginInProgress = false;
                });
        }
    }
})();