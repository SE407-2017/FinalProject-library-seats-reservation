//advice_all.js
angular
    .module('app', ['ngRoute'])
    .controller('reservationAddDetailControl', reservationAddDetailControl)

reservationAddDetailControl.$inject = ['$scope', '$http', '$stateParams'];
function reservationAddDetailControl($scope, $http, $stateParams) {
    $scope.table_id = $stateParams.table_id;
    $scope.seats = {};
    $scope.seats.selected = "-1";
    $scope.doReserve = function() {
        var data = {
            floor_id: $scope.table.floor.id,
            table_id: $scope.table.id,
            seat_id: $scope.seats.selected,
            arrive_at: $('#reserve_time').val()
        };
        $http.post("/api/user/reservation/add", data).success(function(response) {
            console.log(response)
        });
    };
    var myDate = new Date();
    myDate.setHours(22);
    myDate.setMinutes(30);
    $('#reserve_time').datetimepicker('update', new Date());
    $('#reserve_time').datetimepicker('remove');
    $('#reserve_time').datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        autoclose: true,
        startDate: new Date(),
        endDate: myDate,
        startView: 1,
        minuteStep: 30,
        pickerPosition: "bottom-right"
    });
    $http.get("/api/table/" + $scope.table_id + "/detail")
        .then(function(response) {
            $scope.table = response.data;
        });
}