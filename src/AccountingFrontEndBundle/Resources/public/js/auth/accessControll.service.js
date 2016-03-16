(function(){
    'use strict';

    angular
        .module('app.auth')
        .service('accessControlService', accessControlService);

    accessControlService.$inject = [];

    function accessControlService() {
        var self = this;

        self.init = function() {

        }
    }
})();