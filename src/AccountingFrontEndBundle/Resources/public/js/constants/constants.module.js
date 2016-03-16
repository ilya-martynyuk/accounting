(function(){
    'use strict';

    angular
        .module('app.constants', []);

    // "accountigConfig" is taken from temlate. It contains constants generated by symphony dynamically.
    var appConstants = angular.extend(accountigConfig, {
        // Put your own constants here
    });

    angular
        .module('app.constants')
        .constant('appConstants', appConstants)
})();