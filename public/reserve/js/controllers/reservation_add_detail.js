//advice_all.js
angular
    .module('app', ['ngRoute'])
    .controller('reservationAddDetailControl', reservationAddDetailControl)

reservationAddDetailControl.$inject = ['$scope', '$http', '$stateParams'];
function reservationAddDetailControl($scope, $http, $stateParams) {
    $scope.table_id = $stateParams.table_id;
    $scope.seats = {};
    $scope.seats.selected = "-1";
    var myDate = new Date();
    $scope.current_date = myDate.toLocaleDateString();
    $('#datetimepicker').datetimepicker('setStartDate', $scope.current_date);
    myDate.setDate(myDate.getDate() + 1);
    $('#datetimepicker').datetimepicker('setEndDate', myDate.toLocaleDateString());
    $http.get("/api/table/" + $scope.table_id + "/detail")
        .then(function(response) {
            $scope.table = response.data;
        });
}