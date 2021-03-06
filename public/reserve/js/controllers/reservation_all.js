//advice_all.js
angular
    .module('app')
    .controller('reservationAllControl', reservationAllControl)
    .filter('to_trusted', ['$sce', function($sce){
        return function(text) {
            return $sce.trustAsHtml(text);
        };
    }]);

reservationAllControl.$inject = ['$scope', '$http'];
function reservationAllControl($scope, $http) {
    $scope.status_color = ["#32CD32", "#000000", "#00BFFF", "#DC143C"];
    $scope.generate_color = function(id) {
        return {
            "color" : $scope.status_color[id]
        };
    };
    $scope.generate_button = function(id) {
        return "<button type='button' class='btn btn-link' style='margin-top: -4px;' onclick='cancel_reservation("+id+")'>取消预约</button>";
    };
    $scope.render_page = function() {
        $http.get("/api/user/reservation/all")
            .then(function(response) {
                $scope.count = response.data.count;
                $scope.reservation_all = response.data.data;
                console.log($scope.reservation_all);
                setTimeout(function(){
                    $scope.DataTable = $("#all_table").DataTable({
                        "searching": true,
                        language: {
                            processing:     "处理中",
                            lengthMenu:    "每页显示_MENU_条",
                            search:         "搜索:",
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
                        "ordering": false
                    });
                },0);
            });
    }
    $scope.render_page();
}

function cancel_reservation(id) {
    $.get("/api/user/reservation/cancel/"+id, function(result){
        if (result.success == false) {
            toastr.error(result.msg, '错误')
        } else {
            window.location.reload();
        }
    });
}