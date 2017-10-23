angular.module('controllers')
        .controller('infoCtrl', function ($scope, $rootScope, $location, $http, ngDialog, dropdownMenuBarInit, AuthService, Resources, $timeout,$routeParams,$sce) {

            /*
             * MENU CONFIGURATION
             */

            //Images
            $scope.img = [];
            $scope.img.fons = '/img/srcWeb/patterns/fons.png';
            $scope.img.Patterns1_08 = '/img/srcWeb/patterns/pattern3.png';
            $scope.img.loading = '/img/srcWeb/Login/loading.gif';


        //Dropdown Menu Bar
            $rootScope.dropdownMenuBar = null;
            if($rootScope.isLogged){
                var languageId = $rootScope.interfaceLanguageId;
                $rootScope.dropdownMenuBarChangeLanguage = false;//Languages button available
            } else {
                var languageId = $rootScope.contentLanguageUserNonLoged;
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
            //function to change html content language
            $scope.changeLanguage = function (value) {
                $rootScope.contentLanguageUserNonLoged = value;
                window.localStorage.setItem('contentLanguageUserNonLoged', $rootScope.contentLanguageUserNonLoged);
                window.localStorage.setItem('contentLanguageUserNonLogedAbbr', $rootScope.contentLanguageUserNonLogedAbbr);
                Resources.register.get({'section': 'home', 'idLanguage': value}, {'funct': "content"}).$promise
                        .then(function (results) {
                            $rootScope.langabbr = $rootScope.contentLanguageUserNonLogedAbbr;
                            $scope.text = results.data;
                            dropdownMenuBarInit(value);
                        });
            };


            /*
            * HOME VIEW FUNCTIONS
            */

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

            /*
             * Modal for Updates
             * Show if not cookies set
            */
            $http.get($scope.baseurl + 'Register/getLatestUpdateChecked').success(function(response){
                $scope.version = response.latestUpdateChecked.version;
                $scope.showUpdateFooter = (window.localStorage.getItem('updateAccepted') != 'true') && (response.latestUpdateChecked.showPopUp == 1);
                $scope.footerUpdateClass = ($scope.showUpdateFooter) ? "footer-updates" : "footer-cookies-fade";
            });

            $scope.okUpdates = function() {
                window.localStorage.setItem('updateAccepted', true);
                $scope.footerUpdateClass = "footer-cookies-fade";
            };

            // Language
            $rootScope.langabbr = $rootScope.contentLanguageUserNonLogedAbbr;

            //Images
            $scope.img.button1 = 'img/srcWeb/home/about.png';
            $scope.img.button2 = 'img/srcWeb/home/login.png';
            $scope.img.button3 = 'img/srcWeb/home/colab.png';
            $scope.img.button4 = 'img/srcWeb/home/creative-commons.png';

            /*Nuevo código Tarea 7 #Jorge*/
            $scope.img.button5 = 'img/srcWeb/home/faqbuttonbuhohov.png';
            $scope.img.button6 = 'img/srcWeb/home/playbuttonbuhohov.png';
            $scope.img.button7 = 'img/srcWeb/home/downloadbuttonbuhohov.png';

            // Link colors
            $scope.link1color = "#f0a22e";
            $scope.link2color = "#3b93af";
            $scope.link3color = "#edb95d";
            $scope.link4color = "#3b93af";

            /*Nuevo código Tarea 7 #Jorge*/
            $scope.link5color = "#7cb342";
            $scope.link6color = "#ff6e3d";
            $scope.link7color = "#6fa39a";


            // Get content for the home view from ddbb
            Resources.register.get({'section': 'home', 'idLanguage': $rootScope.contentLanguageUserNonLoged}, {'funct': "content"}).$promise
                .then(function (results) {
                    $scope.text = results.data;
                });

            // Llamada Ajax para acceder a la información de la tabla de Videotutoriales.
            $http.post($scope.baseurl + 'Register/getVideotutoriales',{'idLanguage': $rootScope.contentLanguageUserNonLoged}).success(function(response){
              $scope.videotutoriales = response.videotutoriales;
              //console.log($scope.videotutoriales);
            });

            $scope.trustSrc = function(src) {
               return $sce.trustAsResourceUrl(src);
             }

            $scope.linkHome = function () {
                $rootScope.dropdownMenuBarValue = '/home'; //Button selected on this view
                $location.path('/home');
            };

            $scope.linkAbout = function () {
                $rootScope.dropdownMenuBarValue = '/about'; //Button selected on this view
                $location.path('/about');
            };

            $scope.linkLogin = function () {
                $rootScope.dropdownMenuBarValue = '/login'; //Button selected on this view
                $location.path('/login');
            };

            $scope.linkCollaborators = function () {
                $rootScope.dropdownMenuBarValue = '/partners'; //Button selected on this view
                $location.path('/partners');
            };

            $scope.linkCC = function () {
                $rootScope.dropdownMenuBarValue = '/cc'; //Button selected on this view
                $location.path('/cc');
            };

            /* Cambios en la tarea 7 #Jorge*/
            $scope.linkFAQ = function() {
              $rootScope.dropdownMenuBarValue = '/faq'; //Button selected on this view
              $location.path('/faq');
            }

            $scope.linkTutoriales = function() {
              //$rootScope.dropdownMenuBarValue = '/faq'; //Button selected on this view
              $location.path('/tutorials');
            }

            $scope.linkDownloads = function() {
              $rootScope.dropdownMenuBarValue = '/download'; //Button selected on this view
              $location.path('/download');
            }

            $scope.linkColor = function (id, color, inout) {
                switch(id) {
                    case "link-1":
                        $scope.link1color = color;
                        if (!inout) {
                            $scope.img.button1 = 'img/srcWeb/home/about.png';
                        }
                        else {
                            $scope.img.button1 = 'img/srcWeb/home/about-hov.png';
                        }
                        break;

                    case "link-2":
                        $scope.link2color = color;
                        if (!inout) {
                            $scope.img.button2 = 'img/srcWeb/home/login.png';
                        }
                        else {
                            $scope.img.button2 = 'img/srcWeb/home/login-hov.png';
                        }
                        break;

                    case "link-3":
                        $scope.link3color = color;
                        if (!inout) {
                            $scope.img.button3 = 'img/srcWeb/home/colab.png';
                        }
                        else {
                            $scope.img.button3 = 'img/srcWeb/home/colab-hov.png';
                        }
                        break;

                    case "link-4":
                        $scope.link4color = color;
                        if (!inout) {
                            $scope.img.button4 = 'img/srcWeb/home/creative-commons.png';
                        }
                        else {
                            $scope.img.button4 = 'img/srcWeb/home/creative-commons-hov.png';
                        }
                        break;

                    /*Nuevo código Tarea 7 #Jorge */
                    case "link-5":
                      $scope.link5color = color;
                      if(!inout){
                          $scope.img.button5 = 'img/srcWeb/home/faqbuttonbuhohov.png';
                      }
                      else{
                          $scope.img.button5 = 'img/srcWeb/home/faqbuttonbuho.png';
                      }
                      break;

                    case "link-6":
                        $scope.link6color = color;
                        if(!inout){
                            $scope.img.button6 = 'img/srcWeb/home/playbuttonbuhohov.png';
                        }
                        else{
                            $scope.img.button6 = 'img/srcWeb/home/playbuttonbuho.png';
                        }
                        break;

                    case "link-7":
                        $scope.link7color = color;
                        if(!inout){
                            $scope.img.button7 = 'img/srcWeb/home/downloadbuttonbuhohov.png';
                        }
                        else{
                            $scope.img.button7 = 'img/srcWeb/home/downloadbuttonbuho.png';
                        }
                        break;

                    default:
                        break;
                }
            };


        });
