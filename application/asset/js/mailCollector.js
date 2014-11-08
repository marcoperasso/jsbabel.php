function MailCollector($scope, $http, base_url) {
    $scope.emailAddress='';
    $scope.greetings = '';
    $scope.base_url = base_url;
    
    $scope.collectMail = function() {
        var postData = 'email=' + $scope.emailAddress;
        
       $http(
                {   
                    url: $scope.base_url + 'register/collectEmail',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: postData
                }
            ).
            success(function (data, status, headers, config) {
              $scope.greetings = 'Grazie per l\'interesse mostrato!';
            }).
            error(function (data, status, headers, config) {
              $scope.greetings = 'Errore!';
            });
    };
}


