(function($){
    'use strict';

    angular
        .module('app.common')
        .directive('materializeSelect', [
            '$timeout',
            materializeSelect
        ]);

    function materializeSelect($timeout) {
        return {
            restrict: 'A',
            link: link
        }


        function link(scope, element, attrs) {
            $timeout(function(){
                 $(element)
                     .material_select();
            });
        }
    }
})(jQuery);