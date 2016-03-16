(function(){
    'use strict';

    angular
        .module('app.accountPage')
        .controller('HomePageController', homePageController);

    homePageController.$inject = [
        'myOperationsApiService',
        '$rootScope',
        '$state'
    ];

    function homePageController(myOperationsApiService, $rootScope, $state) {
        var self = this,
            currentPurseId = $state.params.purseId;

        self.operations = {};
        self.loadingInProgress = false;

        self.deleteOperation = function(e) {
        }

        self.editOperation = function(e) {

        }

        _loadOperations(currentPurseId);

        function _loadOperations(purseId) {
            self.loadingInProgress = true;

            myOperationsApiService
                .getOperations(purseId)
                .then(function(response){
                    if (response.status !== 200) {
                        return;
                    }

                    self.operations = response.data.data;
                })
                .finally(function(){
                    self.loadingInProgress = false;
                });
        }

        $rootScope
            .$on('$stateChangeStart',
            function(event, toState, toParams, fromState, fromParams, options){
                if (toState.name !== 'account.selected_purse' && toState.name === 'account') {
                    return;
                }

                _loadOperations(toParams.purseId);
            })
    }
})();