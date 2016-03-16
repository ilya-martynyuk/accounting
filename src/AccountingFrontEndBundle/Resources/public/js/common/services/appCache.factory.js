(function(){
    'use strict';

    angular
        .module('app.common')
        .factory('appCacheFactory', appCacheFactory);

    appCacheFactory.$inject = [
        '$angularCacheFactory'
    ];

    function appCacheFactory($angularCacheFactory) {
        var appCache = $angularCacheFactory('accountingCache');

        appCache
            .setOptions({
                maxAge: 3600000
            });

        return appCache;
    }
})();