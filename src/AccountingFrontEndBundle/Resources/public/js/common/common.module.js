(function(){
    'use strict';

    /**
     * @ngdoc module
     * @name app.common
     *
     * @requires ui.router
     *
     * @description
     * Common application module
     */
    angular
        .module('app.common', [
            'ui.router',
            'app.constants'
        ]);
})();