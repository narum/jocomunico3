angular.module('controllers')
  .controller('updatesCtrl', function ($http, $scope, $rootScope, Resources, AuthService, txtContent, $location, $timeout, dropdownMenuBarInit) {

    /*
     * MENU CONFIGURATION
     */

    //Imagenes
    $scope.img = [];
    $scope.img.fons = '/img/srcWeb/patterns/fons.png';
    $scope.img.loading = '/img/srcWeb/Login/loading.gif';
    $scope.img.Patterns1_08 = '/img/srcWeb/patterns/pattern3.png';
    $scope.img.whiteLoading = '/img/icons/whiteLoading.gif';
    $scope.img.Loading_icon = '/img/icons/Loading_icon.gif';
    $scope.img.orangeArrow = '/img/srcWeb/UserConfig/orangeArrow.png';

    //Dropdown Menu Bar
    $rootScope.dropdownMenuBar = null;
    
    var languageId = 1;
    if($rootScope.isLogged){
        languageId = $rootScope.interfaceLanguageId;
        $rootScope.dropdownMenuBarChangeLanguage = false;//Languages button available
    } else {
        languageId = $rootScope.contentLanguageUserNonLoged;
        $rootScope.dropdownMenuBarChangeLanguage = true;//Languages button available
    }

    dropdownMenuBarInit(languageId)
            .then(function () {
                //Choose the buttons to show on bar
                if ($rootScope.isLogged){
                    angular.forEach($rootScope.dropdownMenuBar, function (value) {
                        if (value.href == '/' || value.href == '/about' || value.href == '/panelGroups' || value.href == '/userConfig' || value.href == '/faq' || value.href == '/download' || value.href == '/tips' || value.href == '/privacy') {
                            value.show = true;
                        } else {
                            value.show = false;
                        }
                    });
                }else{
                    angular.forEach($rootScope.dropdownMenuBar, function (value) {
                        if (value.href == '/home' || value.href == '/about' || value.href == '/faq' || value.href == '/download' || value.href == '/tips' || value.href == '/privacy') {
                            value.show = true;
                        } else {
                            value.show = false;
                        }
                    });
                }
            });
    $rootScope.dropdownMenuBarValue = $location.path(); //Button selected on this view
    $rootScope.dropdownMenuBarButtonHide = false;
    //function to change html view
    $scope.go = function (path) {
        $rootScope.dropdownMenuBarValue = path; //Button selected on this view
        $location.path(path);
    };

    console.log($rootScope);
    //function to change html content language
    $scope.changeLanguage = function (value) {
        $rootScope.contentLanguageUserNonLoged = value;
        window.localStorage.setItem('contentLanguageUserNonLoged', $rootScope.contentLanguageUserNonLoged);
        window.localStorage.setItem('contentLanguageUserNonLogedAbbr', $rootScope.contentLanguageUserNonLogedAbbr);
        Resources.register.get({'section': 'updates', 'idLanguage': value}, {'funct': "content"}).$promise
                .then(function (results) {
                    $rootScope.langabbr = $rootScope.contentLanguageUserNonLogedAbbr;
                    $scope.text = results.data;
                    dropdownMenuBarInit(value);
                });
    };


    /*
    * HOME VIEW FUNCTIONS
    */
   
    $scope.linkHome = function () {
            $location.path('/home');
        };

    // Cookies popup
    $scope.acceptcookies = window.localStorage.getItem('cookiesAccepted');

    if ($scope.acceptcookies) {
        $scope.footerclass = "footer-cookies-out";
    }
    else {
        $scope.footerclass = "footer-cookies";
    }

    $scope.okCookies = function () {
        window.localStorage.setItem('cookiesAccepted', true);
        $scope.acceptcookies = true;
        $scope.footerclass = "footer-cookies-fade";
    };

    // Language
    $rootScope.langabbr = $rootScope.contentLanguageUserNonLogedAbbr;

    var idioma = $rootScope.contentLanguageUserNonLoged;
    if (window.localStorage.getItem('contentLanguageUserNonLoged')) {
        idioma = window.localStorage.getItem('contentLanguageUserNonLoged')
        $rootScope.langabbr = window.localStorage.getItem('contentLanguageUserNonLogedAbbr');
    }

    // Get content for the home view from ddbb
    Resources.register.get({'section': 'updates', 'idLanguage': idioma}, {'funct': "content"}).$promise
        .then(function (results) {
            $scope.text = results.data;
        });

    $http.post($scope.baseurl + 'Register/getUpdates',{'idLanguage': idioma}).success(function(response){
      $scope.updates = response.updates;
      console.log($scope.updates);
    });


});
