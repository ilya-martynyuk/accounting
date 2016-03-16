(function(){
    'use strict';

    angular
        .module('app.common')
        .controller('AccountController', AccountController);

    AccountController.$inject = [
        'authService'
    ];

    function AccountController(authService) {
        var self = this;

        self.authService = authService;
    }
})();