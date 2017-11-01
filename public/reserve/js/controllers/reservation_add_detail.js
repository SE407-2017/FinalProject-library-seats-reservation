//advice_all.js
angular
    .module('app', ['ngRoute'])
    .controller('reservationAddDetailControl', reservationAddDetailControl)

reservationAddDetailControl.$inject = ['$scope', '$http', '$stateParams'];
function reservationAddDetailControl($scope, $http, $stateParams) {
    $scope.table_id = $stateParams.table_id;
    $http.get("/api/table/" + $scope.table_id + "/detail")
        .then(function(response) {
            $scope.table = response.data;
        });
}