(function(){
    'use strict';

    angular
        .module('app.auth', [
            'app.common'
        ]);

    angular
        .module('app.auth')
        .run([
            'authService',
            'accessControlService',
            moduleRun
        ]);

    function moduleRun(authService, accessControlService) {
        authService
            .init();

        accessControlService
            .init();
    }
})();