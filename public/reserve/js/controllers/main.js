//main.js
angular
.module('app')
.controller('mainControl', mainControl);

//convert Hex to RGBA
function convertHex(hex,opacity){
  hex = hex.replace('#','');
  r = parseInt(hex.substring(0,2), 16);
  g = parseInt(hex.substring(2,4), 16);
  b = parseInt(hex.substring(4,6), 16);

  result = 'rgba('+r+','+g+','+b+','+opacity/100+')';
  return result;
}

mainControl.$inject = ['$scope', '$http'];
function mainControl($scope, $http) {
    $http.get("/api/user/info")
        .then(function(response) {
            $scope.reservation = response.data.reservation;
            $scope.reservations_count = response.data.reservation.all_count;
            $scope.user_info = response.data.user_info;
        });
}