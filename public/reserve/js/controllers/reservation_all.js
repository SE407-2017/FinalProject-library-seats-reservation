//advice_all.js
angular
    .module('app')
    .controller('reservationAllControl', reservationAllControl)

reservationAllControl.$inject = ['$scope', '$http'];
function reservationAllControl($scope, $http) {
    $scope.status_color = ["#32CD32", "#000000", "#00BFFF", "#DC143C"];
    $scope.generate_color = function(id) {
        return {
            "color" : $scope.status_color[id]
        };
    };
    $http.get("/api/user/reservation/all")
        .then(function(response) {
            $scope.count = response.data.count;
            $scope.reservation_all = response.data.data;
            setTimeout(function(){
                $scope.DataTable = $("#all_table").DataTable({
                    "searching": true,
                    language: {
                        processing:     "处理中",
                        search:         "搜索:",
                        lengthMenu:    "每页显示_MENU_条",
                        info:           "显示 _START_ 到 _END_ 条, 共 _TOTAL_ 条",
                        infoEmpty:      "显示 0 到 0 条, 共 0 条",
                        loadingRecords: "正在加载...",
                        zeroRecords:    "没有预约",
                        emptyTable:     "没有预约",
                        paginate: {
                            first:      "第一页",
                            previous:   "上一页",
                            next:       "下一页",
                            last:       "最后页"
                        },
                    },
                    "ordering": true
                });
            },0);
        });
}