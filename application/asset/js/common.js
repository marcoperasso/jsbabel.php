$(function () {
    $('body')
            .css({"min-height": $(window).innerHeight()})
            .attr("ng-app", "")
            .attr("ng-controller", "jsbController");
    ;
    $(window).resize(function () {
        $('body').css({"min-height": $(window).innerHeight()});
    });
    var jContainer = $('div.content');
    $.get("templates/visualheader.html", null, function (data) {
        jContainer.prepend($(data));
    })

    $.get("templates/footer.html", null, function (data) {
        jContainer.append($(data));
    })

    $.getScript("//ajax.googleapis.com/ajax/libs/angularjs/1.2.26/angular.min.js");
});

function jsbController($scope, $http) {
    $http.get("home/data")
            .success(function (response) {
                $scope.data = response;
//give a chance to the page to specify its own data request
                if (window.controllerRequest)
                {
                    $http.get(window.controllerRequest).success(function (response)
                    {
                        $scope.pageData = response;
                        //notify the page that the controller has been loaded
                        //use timeout to postpone the event processing to the moment angular has finished rendering
                        if (window.controllerReady)
                            setTimeout(function () {
                                window.controllerReady($scope);
                            }, 1);
                    })
                }
                else if (window.controllerReady)
                    window.controllerReady($scope);//notify the page that the controller has been loaded
            });
}
