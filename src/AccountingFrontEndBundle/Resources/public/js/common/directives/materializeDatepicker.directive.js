(function($){
    'use strict';

    angular
        .module('app.common')
        .directive('materializeDatepicker', [
            '$timeout',
            materizeDatepicker
        ]);

    function materizeDatepicker($timeout) {
        return {
            restrict: 'A',
            link: link
        }

        function link(scope, element, attrs) {
            $timeout(function(){
                 $(element)
                    .pickadate({
                        selectMonths: true,
                        selectYears: 15
                    });
            });
        }
    }
})(jQuery);