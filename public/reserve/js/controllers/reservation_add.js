//advice_all.js
angular
    .module('app')
    .controller('reservationAddControl', reservationAddControl)

reservationAddControl.$inject = ['$scope', '$http'];
function reservationAddControl($scope, $http) {
    $scope.refresh_tables = function(floor) {
        $http.get("/api/user/tables/status/"+floor)
            .then(function(response) {
                $scope.tables = response.data.tables;
            });
    };
    $scope.reserve = function(table_id) {
        window.location.href = "#!/reservation/add/" + table_id;
    };
    $scope.floor_change = function() {
        $scope.refresh_tables($scope.selected_floor.id);
    };
    $http.get("/api/floors/get")
        .then(function(response) {
            $scope.floors = response.data.data;
            $scope.selected_floor = $scope.floors[0];
            $scope.refresh_tables($scope.floors[0].id)
        });
}