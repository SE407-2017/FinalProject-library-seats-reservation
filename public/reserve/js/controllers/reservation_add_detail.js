//advice_all.js
angular
    .module('app', ['ngRoute'])
    .controller('reservationAddDetailControl', reservationAddDetailControl)

reservationAddDetailControl.$inject = ['$scope', '$http', '$stateParams'];
function reservationAddDetailControl($scope, $http, $stateParams) {
    $scope.table_id = $stateParams.table_id;
    $http.get("/api/floors/get")
        .then(function(response) {
            $scope.floors = response.data.data;
            $scope.refresh_tables($scope.floors[0].id)
        });
}