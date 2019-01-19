angular.module('controllers')
        .controller('panelCtrl', function ($scope, $rootScope, $cookies, txtContent, $location, $http, ngDialog, dropdownMenuBarInit, AuthService, Resources, $timeout,$route) {
            // Comprobación del login   IMPORTANTE!!! PONER EN TODOS LOS CONTROLADORES
            if (!$rootScope.isLogged) {
                $location.path('/home');
                $rootScope.dropdownMenuBarValue = '/home'; //Dropdown bar button selected on this view
            }
            // Pedimos los textos para cargar la pagina
            txtContent("panelgroup").then(function (results) {
                $scope.content = results.data;
                getFolders();
            });
            txtContent("historySentencesFold").then(function (results) {
                $scope.editHistoricFolderContent = results.data;
                $scope.createFolderContentTitle = true; //Change the modal title to create folder or edit folder
            });
            txtContent("searchArasaacImgs").then(function (results) {
                $scope.searchArasaacImgsContent = results.data;
            });
            
            // Check if the user has already added the Example panels
            $scope.alreadyExamplePanels = false;
           if ($cookies.get('alreadyExamplePanels')) {
                $scope.alreadyExamplePanels = true;
            }              

            //Dropdown Menu Bar
            $rootScope.dropdownMenuBar = null;
            $rootScope.dropdownMenuBarButtonHide = false;
            $rootScope.dropdownMenuBarValue = '/panelGroups'; //Button selected on this view
            $rootScope.dropdownMenuBarChangeLanguage = false;//Languages button available

            //Choose the buttons to show on bar
            dropdownMenuBarInit($rootScope.interfaceLanguageId)
                    .then(function () {
                        //Choose the buttons to show on bar
                        angular.forEach($rootScope.dropdownMenuBar, function (value) {
                            if (value.href == '/' || value.href == '/panelGroups' || value.href == '/userConfig' || value.href == '/faq' || value.href == '/tips' || value.href == '/privacy' || value.href == 'logout') {
                                value.show = true;
                            } else {
                                value.show = false;
                            }
                        });
                    });
            //function to change html view
            $scope.go = function (path) {
                if (path == 'logout') {
                    $('#logoutModal').modal('toggle');
                } else {
                    $rootScope.dropdownMenuBarValue = path; //Button selected on this view
                    $location.path(path);
                }
            };
            
            //function to reload the view
            $scope.reloadView = function () 
            {
                location.reload();
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

            Resources.main.get({'section': 'home', 'idLanguage': $rootScope.interfaceLanguageId}, {'funct': "content"}).$promise
            .then(function (results) {
                $scope.text = results.data;
            });

            //Log Out Modal
            Resources.main.get({'section': 'logoutModal', 'idLanguage': $rootScope.interfaceLanguageId}, {'funct': "content"}).$promise
                    .then(function (results) {
                        $scope.logoutContent = results.data;
                    });
            $scope.logout = function () {
                $timeout(function () {
                    AuthService.logout();
                }, 1000);
            };


            //Content Images and backgrounds
            $scope.img = [];
            $scope.img.fons = '/img/srcWeb/patterns/fons.png';
            $scope.img.lowSorpresaFlecha = '/img/srcWeb/Mus/lowSorpresaFlecha.png';
            $scope.img.Patterns1_08 = '/img/srcWeb/patterns/pattern3.png';
            $scope.img.Patterns4 = '/img/srcWeb/patterns/pattern4.png';
            $scope.img.Patterns6 = '/img/srcWeb/patterns/pattern6.png';
            $scope.img.loading = '/img/srcWeb/Login/loading.gif';
            $scope.img.addPhoto = '/img/icons/add_photo.png';
            $scope.img.addPhotoSelected = '/img/icons/add_photo_selected.png';
            $scope.img.whiteLoading = '/img/icons/whiteLoading.gif';
            $scope.finished = true;
            $scope.viewActived = false;

            //User sentence folders
            var getFolders = function(){
                Resources.main.get({'funct': "getSentenceFolders"}).$promise
                .then(function (results) {
                    $scope.historicFolders=[];
                    $scope.historicFolders.push({'ID_Folder':'-1', 'ID_SFUser':$rootScope.userId, 'folderDescr':'', 'folderName':$scope.content.historyTodayFolder, 'imgSFolder':'img/pictos/hoy.png', 'folderColor':'dfdfdf', 'folderOrder':'0.1'});
                    $scope.historicFolders.push({'ID_Folder':'-7', 'ID_SFUser':$rootScope.userId, 'folderDescr':'', 'folderName':$scope.content.historyLastWeekFolder, 'imgSFolder':'img/pictos/semana.png', 'folderColor':'dfdfdf', 'folderOrder':'0.2'});
                    $scope.historicFolders.push({'ID_Folder':'-30', 'ID_SFUser':$rootScope.userId, 'folderDescr':'', 'folderName':$scope.content.historyLastMonthFolder, 'imgSFolder':'img/pictos/mes.png', 'folderColor':'dfdfdf', 'folderOrder':'0.3'});
                    angular.forEach(results.folders, function (value) {
                        value.folderOrder = parseInt(value.folderOrder, 10);
                        $scope.historicFolders.push(value);
                    });
                    $scope.historicFolders.sort(function(a, b){return a.folderOrder-b.folderOrder});
                    $scope.showUpDownButtons=true;
                    $scope.viewActived = true;
                });
            };
            //Delet historic sentences 30 days old
            Resources.main.get({'funct': "getHistoric"});

            //Up folder order
            $scope.upFolder = function (order, folderId) {
                order = parseInt(order, 10); //string to integer
                if (order > 1) {
                    $scope.showUpDownButtons=false;
                    Resources.main.save({'ID_Folder': folderId}, {'funct': "upHistoricFolder"}).$promise
                    .then(function (results) {
                        getFolders();
                    });
                }
            };
            //Down folder order
            $scope.downFolder = function (order, folderId) {
                order = parseInt(order, 10); //string to integer
                if (order < $scope.historicFolders[$scope.historicFolders.length-1].folderOrder) {
                    $scope.showUpDownButtons=false;
                    Resources.main.save({'ID_Folder': folderId}, {'funct': "downHistoricFolder"}).$promise
                    .then(function (results) {
                        getFolders();
                    });
                }
            };
            //go to folder view
            $scope.goSentencesFolder = function (folder) {
                $timeout(function () {
                    $location.path('/sentencesFolder/' + folder);
                }, 1000);
                $rootScope.dropdownMenuBarValue = '';
            }

            //Scrollbar inside div
            $scope.$on('scrollbar.show', function () {
//                console.log('Scrollbar show');
            });

            $scope.$on('scrollbar.hide', function () {
//                console.log('Scrollbar hide');
            });
            $scope.$on('scrollbar.show', function () {
//                console.log('Scrollbar show');
            });

        //CreateFolder
        $scope.createHistoricFolder = function(){
          var online = navigator.onLine;
          if(online) $scope.internetOn=true; else $scope.internetOn=false;
            $('#editHistoricFolderModal').modal('toggle');//Show modal
        };
        $scope.newFolder={};
        $scope.saveFolder = function(){
            if ($scope.newFolder.folderColor == null){
                $scope.newFolder.folderColor='FFFFFF';
            }
            Resources.main.save({'folderName':$scope.newFolder.folderName,'imgSFolder':$scope.newFolder.imgSFolder,'folderColor':$scope.newFolder.folderColor},{'funct': "createSentenceFolder"}).$promise
            .then(function (results) {
                $scope.newFolder={};
                getFolders();

            });
        };
        
        /***************************************************
        *
        *  ARASAAC Images Functions
        *
        ***************************************************/

        $scope.addedImage={};
        $scope.addedImage.path = "";
        $scope.addedImage.name = "";
        $scope.deletedImage={};
        $scope.deletedImage.path = "";
        $scope.deletedImage.id = 0;
        $scope.uploadedOK = "waiting";
        $scope.deletedOK = "waiting";

        // Open search popup to add images
        $scope.popUpAddArasaacImgs = function(){
            $scope.addedImage.path = "";
            $scope.addedImage.name = "";
            $scope.uploadedOK = "waiting";
            var online = navigator.onLine;
            if(online) $scope.internetOn=true; 
            else $scope.internetOn=false;
            $('#arasaacImgsSearchModal').modal('toggle');//Show modal
        };
        
        $scope.searchImgArasaac = function (name, type)
        {
            $timeout.cancel($scope.searchArasaacTimeout);
            $scope.searchArasaacTimeout = $timeout(function () {
                $scope.searchStartImgArasaac(name, type);
            }, 500);
        };

        /*
         * Return the ARASAAC IMGS from the ARASAAC server. 
         * There are two types of search in color or in black & white
         */
         $scope.searchStartImgArasaac = function (name, type) {
            var url = $scope.baseurl + "ARASAAC/search";
            var postdata = {name: name, type: type};
            $http.post(url, postdata).
            success(function (response)
            {
                $scope.imgData = response.data;
                // console.log(response.data);
            });
         };
         
         $scope.uploadARASAACImage = function (path, name) {
            $scope.uploadedOK = "waiting";
            var url = $scope.baseurl + "ARASAAC/upload";
            var postdata = {path: path, name: name};
            $http.post(url, postdata).
            success(function (response)
            {
                $scope.uploadedOK = response.status;
                console.log(response.status);
            });
         };
         
         // Open popup to delete images
        $scope.popUpDeleteImgs = function(){
            $scope.deletedImage.path = "";
            $scope.deletedImage.id = 0;
            $scope.deletedOK = "waiting";
            $('#imgsDeleteModal').modal('toggle');//Show modal
        };
        
        $scope.searchImgDelete = function (name)
        {
            $timeout.cancel($scope.searchDeleteTimeout);
            $scope.searchDeleteTimeout = $timeout(function () {
                $scope.searchStartImgDelete(name);
            }, 500);
        };

        /*
         * Return the ARASAAC IMGS from the ARASAAC server. 
         * There are two types of search in color or in black & white
         */
         $scope.searchStartImgDelete = function (name) {
            var url = $scope.baseurl + "ARASAAC/searchDelete";
            var postdata = {name: name};
            $http.post(url, postdata).
            success(function (response)
            {
                $scope.imgData = response.data;
                // console.log(response.data);
            });
         };
         
         $scope.deleteImage = function (id, path) {
            $scope.deletedOK = "waiting";
            var url = $scope.baseurl + "ARASAAC/deleteImg";
            var postdata = {path: path, id: id};
            $http.post(url, postdata).
            success(function (response)
            {
                $scope.deletedOK = response.status;
                console.log(response.status);
                $scope.searchStartImgDelete($scope.imgAACSearch);
            });
         };
         
         
        /***************************************************
        *
        *  editFolders functions
        *
        ***************************************************/
        $scope.CreateBoard = function (ID_GB) {
            $scope.idGroupBoard = ID_GB;
            var URL = $scope.baseurl + "PanelGroup/getPanelGroupInfo";
            //alert($scope.idGroupBoard);
            var postdata = {idGroupBoard: $scope.idGroupBoard};
            $http.post(URL, postdata).
                success(function (response)
                {
                    $scope.CreateBoardData = {CreateBoardName: '', height: response.defHeight.toString(), width: response.defWidth.toString(), idGroupBoard: response.ID_GB};
                    $scope.CreateBoardData.height = $scope.range(10)[response.defHeight - 1].valueOf();
                    $scope.CreateBoardData.width = $scope.range(10)[response.defWidth - 1].valueOf();

                    $('#ConfirmCreateBoard').modal({backdrop: 'static'});
                });

        };
        $scope.confirmCreateBoard = function () {
            URL = $scope.baseurl + "Board/newBoard";
            $http.post(URL, $scope.CreateBoardData).success(function (response)
            {
                $scope.editPanel($scope.idGroupBoard);
            });
        };
        /*
         * Return uploaded images from database. There are two types, the users images an the arasaac (not user images)
         */
         $scope.searchImg = function (name, typeImgEditSearch) {
       var URL = "";
       switch (typeImgEditSearch)
       {
           case "Arasaac":
               URL = $scope.baseurl + "ImgUploader/getImagesArasaac";
               break;
           case "Uploads":
               URL = $scope.baseurl + "ImgUploader/getImagesUploads";
               break;
       }
       var postdata = {name: name};
       $http.post(URL, postdata).
           success(function (response)
           {
               $scope.imgData = response.data;
           });
         }

        //get all the photos attached to the pictos
        $scope.searchFoto = function (name)
        {
            var URL = $scope.baseurl + "SearchWord/getDBAll";
            var postdata = {id: name};
            //Request via post to controller search data from database
            $http.post(URL, postdata).
                success(function (response)
                {
                    $scope.allImg = response.data;
                });
        };
        // Upload and resize the image
        $scope.uploadFile = function () {
            $scope.myFile = document.getElementById('file-input').files;
            $scope.uploading = true;
            var i;
            var uploadUrl = $scope.baseurl + "ImgUploader/upload";
            var fd = new FormData();
            fd.append('vocabulary', angular.toJson(false));
            for (i = 0; i < $scope.myFile.length; i++) {
                fd.append('file' + i, $scope.myFile[i]);
            }
            $http.post(uploadUrl, fd, {
                headers: {'Content-Type': undefined}
            })
                .success(function (response) {
                    $scope.uploading = false;
                    if (response.error) {
                        //open modal
                        console.log(response.errorText);
                        $scope.errorText = response.errorText;
                        $('#errorImgModal').modal({backdrop: 'static'});
                    }
                })
                .error(function (response) {
                    //alert(response.errorText);
                });
        };

            $scope.range = function ($repeatnum)
            {
                var n = [];
                for (i = 1; i < $repeatnum; i++)
                {
                    n.push(i);
                }
                return n;
            };

            $scope.initPanelGroup = function () {
                var URL = $scope.baseurl + "PanelGroup/getUserPanelGroups";

                $http.post(URL).
                        success(function (response)
                        {
                            $scope.panels = response.panels;
                        });
            };
            $scope.initPanelGroup();
            $scope.copyGroupBoard = function (idboard) {
                $scope.idboardToCopy = idboard;
                $scope.isLoged = "false";
                $scope.state = "";
                $scope.state2 = "";
                $scope.usernameCopyPanel = "";
                $scope.passwordCopyPanel = "";
                $scope.idUser = null;
                $('#ConfirmCopyGroupBoard').modal({backdrop: 'static'});
            };
            $scope.copyVocabulary = function () {
                $scope.isLoged = "false";
                $scope.state = "";
                $scope.state2 = "";
                $scope.usernameCopyPanel = "";
                $scope.passwordCopyPanel = "";
                $scope.idUser = null;
                $('#ConfirmCopyVocabulary').modal({backdrop: 'static'});
            };
            $scope.changeUser = function () {
                $scope.isLoged = "false";
                $scope.state = "";
                $scope.state2 = "";
                $scope.usernameCopyPanel = "";
                $scope.passwordCopyPanel = "";
                $scope.idUser = null;
            }
            $scope.login = function () {
                if ($scope.usernameCopyPanel == "") {
                    $scope.state = 'has-warning';
                } else {
                    $scope.state = '';
                }
                if ($scope.passwordCopyPanel == "") {
                    $scope.state2 = 'has-warning';
                } else {
                    $scope.state2 = '';
                }
                if ($scope.usernameCopyPanel != "" && $scope.passwordCopyPanel != "") {
                    $scope.isLoged = "loading";
                    var postdata = {user: $scope.usernameCopyPanel, pass: $scope.passwordCopyPanel};
                    var url = $scope.baseurl + "PanelGroup/loginToCopy";
                    $http.post(url, postdata).
                            success(function (response)
                            {
                                if (response.userID != null) {
                                    $scope.idUser = response.userID;
                                    $scope.isLoged = "true";
                                } else {
                                    $scope.state = 'has-error';
                                    $scope.state2 = 'has-error';
                                    $scope.isLoged = "false";
                                }
                            });
                }
            };
            $scope.ConfirmCopyGroupBoard = function () {
                var URL = $scope.baseurl + "PanelGroup/copyGroupBoard";
                var postdata = {id: $scope.idboardToCopy, user: $scope.idUser};
                $scope.finished = false;
                $http.post(URL, postdata).success(function (response)
                {
                    $scope.finished = true;
                });
            };
            $scope.ConfirmCopyVocabulary = function () {
                var URL = $scope.baseurl + "AddWord/copyUserVocabulary";
                var postdata = {user: $scope.idUser};
                $scope.finished = false;
                $http.post(URL, postdata).success(function (response)
                {
                    $scope.finished = true;
                });
            };
            $scope.newPanellGroup = function () {
                $scope.CreateBoardData = {GBName: '', defH: 5, defW: 5, imgGB: ""};
                $('#ConfirmCreateGroupBoard').modal({backdrop: 'static'});
            };

            $scope.ConfirmNewPanellGroup = function () {
                var URL = $scope.baseurl + "PanelGroup/newGroupPanel";
                $http.post(URL, $scope.CreateBoardData).success(function (response)
                {
                    $rootScope.editPanelInfo = {idBoard: response.idBoard};
                    $timeout(function () {
                        $location.path('/');
                    }, 1000);
                });
            };


            $scope.editPanel = function (idGB) {
                var postdata = {ID_GB: idGB};
                var URL = $scope.baseurl + "PanelGroup/getPanelToEdit";

                $http.post(URL, postdata).
                        success(function (response)
                        {
                            $scope.id = response.id;
                            if ($scope.id === null) {//MODIF:--Modal no tiene panel pricipal, se añade uno para que pueda hacer algo (no se si se puede hacer, ya que el modal creo que se ira. Si pasa esto meter una variable en el objeto editpanelinfo)
                                $scope.id = response.boards[0].ID_Board;
                            }
                            // Put the panel to edit info, and load the edit panel
                            $rootScope.editPanelInfo = {idBoard: $scope.id};
                            $timeout(function () {
                                $location.path('/');
                            }, 1000);
                        });
            };

            $scope.setPrimary = function (idGB) {
                var postdata = {ID_GB: idGB};
                var URL = $scope.baseurl + "PanelGroup/setPrimaryGroupBoard";

                $http.post(URL, postdata).
                        success(function (response)
                        {
                            $scope.initPanelGroup();
                        });
            };

            $scope.changeGroupBoardName = function (nameboard, idgb)
            {
                var postdata = {Name: nameboard, ID: idgb};
                var URL = $scope.baseurl + "PanelGroup/modifyGroupBoardName";
                $http.post(URL, postdata).
                        success(function (response)
                        {

                        });
            };
            $scope.$on('scrollbarPanel', function (ngRepeatFinishedEvent) {
                $scope.$broadcast('rebuild:me');
            });

            $scope.$on('scrollbarHistoric', function (ngRepeatFinishedEvent) {
                $scope.$broadcast('rebuild:meH');
            });



            $scope.addWord = function (newModif, addWordType) {
                if (newModif == 1) {
                    $rootScope.addWordparam = {newmod: newModif, type: addWordType};
                    $timeout(function () {
                        $location.path('/addWord');
                    }, 1000);
                }
                if (newModif == 0) {
                    switch(addWordType){
                        case("edit"):
                            $rootScope.addWordparam = {newmod: newModif, type: addWordType};
                            $('#ConfirmEditAddWord').modal({backdrop: 'static'});
                            break;
                        case("copy"):
                            $scope.copyVocabulary();
                            break;
                }
                }

            };
            
            $scope.addVerb = function (newModif){
                if (newModif == 1) {
                    $rootScope.addWordparam = {newmod: false, type: false};
                    $timeout(function () {
                        $location.path('/addVerb');
                    }, 1000);
                }
            };

            $scope.selectAddWordEdit = function (newModif, id, pictoType) {
                if(pictoType === 'verb'){
                    $rootScope.addWordparam = {newmod: true, type: id};
                    $timeout(function () {
                        $location.path('/addVerb');
                    }, 1000);
                }else{
                    $rootScope.addWordparam = {newmod: newModif, type: id};
                    $timeout(function () {
                        $location.path('/addWord');
                    }, 1000);
                }
            };

	    $scope.searchDoneAddWord = function (name, Searchtype)
            {

                var URL = "";
                var postdata = {id: name};
                //Radio button function parameter, to set search type
                switch (Searchtype)
                {
                    case "Tots":
                        URL = $scope.baseurl + "AddWord/getDBAll";
                        break;
                    case "Noms":
                        URL = $scope.baseurl + "AddWord/getDBNames";
                        break;
                    case "Verb":
                        URL = $scope.baseurl + "AddWord/getDBVerbs";
                        break;
                    case "Adj":
                        URL = $scope.baseurl + "AddWord/getDBAdj";
                        break;
                    case "Exp":
                        URL = $scope.baseurl + "AddWord/getDBExprs";
                        break;
                    case "Altres":
                        URL = $scope.baseurl + "AddWord/getDBOthers";
                        break;
                    default:
                        URL = $scope.baseurl + "AddWord/getDBAll";
                }
                //Request via post to controller search data from database
                $http.post(URL, postdata).
                        success(function (response)
                        {
                            $scope.dataWordAddWord = response.data;
                        });
            };
            $scope.searchAddWord = function (name, Searchtype)
            {
                $timeout.cancel($scope.searchTimeout);
                $scope.searchTimeout = $timeout(function () {
                    $scope.searchDoneAddWord(name, Searchtype);
                }, 500);
            };

            $scope.SearchTypeAddWord = "Tots";
            $scope.style_changes_title = '';

            // Activate information modals (popups)
            $scope.toggleInfoModal = function (title, text) {
                $scope.infoModalContent = text;
                $scope.infoModalTitle = title;
                $scope.style_changes_title = 'padding-top: 2vh;';
                $('#infoModal').modal('toggle');
            };
            /* Browser detection
            * @rjlopezdev
            */
            $scope.isNotChrome = function () {
              //If cookie is not saved, show modal
              if($cookies.get('browserAdvice') != 'true'){
                $scope.isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
                if($scope.isChrome){} else {
                  $scope.toggleInfoModal($scope.content.chromeAdviceTitle, $scope.content.chromeAdviceBody);
                }
              }
              //Save cookie unconditionally after show modal if neccessary
              $cookies.put('browserAdvice', 'true');
            };

            /* Enable/Disable Historic
             * @rjlopezdev
             */
            $scope.HistoricState;


            $scope.getHistorialState = function () {
                var URL = $scope.baseurl + "Historic/getHistorialState";
                $http.post(URL).success(function (response) {
                    $scope.HistoricState = (response.state === '1') ? true : false;
                    console.log(response.state);
                });
            };

            $scope.getHistorialState();
            //Change current Historic State [cfgHIstorialState]
            $scope.changeHistorialState = function () {
                //!$scope.HistoricState;
                /* Call to 'Historic/changeHistorialState' & params.
                 * - newState : new Historic State (enable or disable)
                 */
                $('#HistoricModal').modal('hide');
                var URL = $scope.baseurl + "Historic/changeHistorialState";
                $http.post(URL
                    , {
                        newState: ($scope.HistoricState) ? 0 : 1
                    })
                    .success(function (response) {
                        $scope.getHistorialState();
                        console.log('hola');
                    });
            };

            $scope.enable_disableHistoric = function () {
                //Show YES/NO Modal (disable Historial?)
                if ($scope.HistoricState === true) {
                    $('#HistoricModal').modal('toggle');
                    //Show Modal (enable Historial)
                } else if ($scope.HistoricState === false) {
                    //console.log($scope.HistoricState);
                    $scope.toggleInfoModal($scope.content.modalInfoTitle, $scope.content.historialInfoEnable);
                    $scope.changeHistorialState();
                }
            };
            
            //******************************
            // BACKUP FUNCTIONS
            //******************************
            
            $scope.nocheckbox = false;

            //muestra el modal de recuperacion de backup
                      $scope.showRecoverBackupModal=function(foldername){
                        $scope.foldername=foldername;
                        $('#RecoverBackupModal').modal('toggle');
                      }

                      $scope.recparcialBackupCall_OW=function(BackupRoute,foldername,keycounts){
                        var postdata = {overwrite: true, folder:foldername, keycounts:keycounts};
                        $http.post("BackupController/"+BackupRoute,postdata).success(function (results) {
                          console.log(results.data);
                        });
                      }
                      $scope.recparcialBackupCall_NOW=function(BackupRoute,foldername, keycounts){
                        var postdata = {overwrite: false,folder: foldername, keycounts:keycounts};
                        $http.post("BackupController/"+BackupRoute,postdata).success(function (results) {
                          console.log(results.data);
                        });
                      }
                      //funcion que se llama en el click del boton recuperar parcial
                      $scope.recparcialBackup_OW=function(image,voc,folder,cfg,panelb,foldername){
                        if((typeof image==='undefined'&& typeof voc==='undefined'&& typeof folder==='undefined'&&
                         typeof cfg==='undefined'&& typeof panelb==='undefined')||(!image&&!voc&&!folder&&!cfg&&!panelb))
                        {
                          $scope.toggleInfoModal("Information", "There was an error.");
                        }else{
							$scope.viewActived=false;
                            $http.get("BackupController/getkeycounts").success(function (results) {
                                var keycounts = results.data;
                                if(panelb){
                                  image=true;
                                  voc=true;
                                  folder=true;
                                  $scope.recparcialBackupCall_OW('recpictos',foldername, keycounts);
                                  $scope.recparcialBackupCall_OW('recimages',foldername, keycounts);
                                  if(cfg)$scope.recparcialBackupCall_OW('reccfg',foldername, keycounts);
                                  
                                  $timeout(function () {
                                      var postdata = {overwrite: true, folder:foldername, keycounts:keycounts};
                                      $http.post("BackupController/recfolder",postdata).success(function (results) {
                                          $http.post("BackupController/recpanels",postdata).success(function (results) {
                                          
                                            $route.reload();
                                          
                                          });
                                      });
                                  }, 3000);
                                  
                                }else{
                                    if(cfg) $scope.recparcialBackupCall_OW('reccfg',foldername, keycounts);
                                    if (folder) {
                                        $scope.recparcialBackupCall_OW('recvocabulary',foldername, keycounts);
                                        $scope.recparcialBackupCall_OW('recimages',foldername, keycounts);
                                        
                                        $timeout(function () {
                                            var postdata = {overwrite: true, folder:foldername, keycounts:keycounts};
                                            $http.post("BackupController/recfolder",postdata).success(function (results) {
                                          
                                                $scope.viewActived=false;
                                                $route.reload();
                                          
                                            });
                                        }, 3000);
                                    }
                                    else if(voc && !folder) { 
                                        $scope.recparcialBackupCall_OW('recvocabulary',foldername, keycounts);
                                        $scope.recparcialBackupCall_OW('recimages',foldername, keycounts);
                                    }
                                    else if(image && !voc && !folder)$scope.recparcialBackupCall_OW('recimages',foldername, keycounts);
                                  
                                    setTimeout(function(){ $route.reload(); }, 3000);
                                }
                            }
                          );
                      }
                    }
                      $scope.recparcialBackup_NOW=function(image,voc,folder,cfg,panelb,foldername){
                        if((typeof image==='undefined'&& typeof voc==='undefined'&& typeof folder==='undefined'&&
                         typeof cfg==='undefined'&& typeof panelb==='undefined')||(!image&&!voc&&!folder&&!cfg&&!panelb)){
                          $scope.toggleInfoModal("Information", "There was an error.");
                        }else{
							$scope.viewActived=false;
                            $http.get("BackupController/getkeycounts").success(function (results) {
                                var keycounts = results.data;
                                if(panelb){
                                  image=true;
                                  voc=true;
                                  folder=true;
                                  $scope.recparcialBackupCall_NOW('recpictos',foldername, keycounts);
                                  $scope.recparcialBackupCall_NOW('recimages',foldername, keycounts);
                                  if(cfg)$scope.recparcialBackupCall_NOW('reccfg',foldername, keycounts);
                                  
                                  $timeout(function () {
                                      var postdata = {overwrite: false, folder:foldername, keycounts:keycounts};
                                      $http.post("BackupController/recfolder",postdata).success(function (results) {
                                          $http.post("BackupController/recpanels",postdata).success(function (results) {
                                          
											$route.reload();
                                          
                                          });
                                      });
                                  }, 3000);
                                  
                                }else{
                                    if(cfg) $scope.recparcialBackupCall_NOW('reccfg',foldername, keycounts);
                                    if (folder) {
                                        $scope.recparcialBackupCall_NOW('recvocabulary',foldername, keycounts);
                                        $scope.recparcialBackupCall_NOW('recimages',foldername, keycounts);
                                        
                                        $timeout(function () {
                                            var postdata = {overwrite: false, folder:foldername, keycounts:keycounts};
                                            $http.post("BackupController/recfolder",postdata).success(function (results) {
                                          
                                                $scope.viewActived=false;
                                                $route.reload();
                                          
                                            });
                                        }, 3000);
                                    }
                                    else if(voc && !folder) { 
                                        $scope.recparcialBackupCall_NOW('recvocabulary',foldername, keycounts);
                                        $scope.recparcialBackupCall_NOW('recimages',foldername, keycounts);
                                    }
                                    else if(image && !voc && !folder)$scope.recparcialBackupCall_NOW('recimages',foldername, keycounts);
                                  
                                    setTimeout(function(){ $route.reload(); }, 3000);
                                }
                            }
                          );
                            }
                        }
                      $scope.checkboxparcial=function(){
                        $("#limgrec").attr("checked",true)
                        $("#lvoc").attr("checked",true)
                        $("#lfold").attr("checked",true)
                      }
                      //funcion que llama al backend para hacer un backup total
                      $scope.totalBackup=function(){
                        var promise = $http.get('BackupController');
                        promise.then(function(results) {
                          $scope.backup=results.data.data;
                          console.log(results);
                          $('#DownloadBackup').modal('toggle');
                        });

                      }
                      $scope.DownloadBackup=function(backup){
                        if (navigator.appVersion.indexOf("Win")!=-1)
                         DownloadUrl="DownloadBackupWin/backup/"+backup;
                         else {
                         DownloadUrl="DownloadBackup/backup/"+backup;
                         }
                         console.log(DownloadUrl)
                        setTimeout(function(){
                          window.location.href=DownloadUrl;
                          $('#DownloadBackup').modal('toggle');
                        }, 2000);
                      }
                      $scope.uploadBackup = function () {
                          $scope.myFile = document.getElementById('file-backup').files;
                          $scope.uploading = true;
                          var i;var uploadUrl;
                          if (navigator.appVersion.indexOf("Win")!=-1){
                          uploadUrl=$scope.baseurl + "ImgUploader/uploadBackup";
                        }else {
                             uploadUrl= $scope.baseurl + "ImgUploader/uploadBackup";

                           }
                           console.log(uploadUrl);
                          var fd = new FormData();
                          fd.append('vocabulary', angular.toJson(false));
                          for (i = 0; i < $scope.myFile.length; i++) {
                              fd.append('file' + i, $scope.myFile[i]);
                          }
                          $http.post(uploadUrl, fd, {
                              headers: {'Content-Type': undefined}
                          })
                                  .success(function (response) {
                                      $scope.uploading = false;
                                      console.log(response);
                                      if (response === null || response.error) {
                                          console.log(response.errorText);
                                          $scope.toggleInfoModal($scope.content.modalInfoTitle, $scope.content.backupWrongFile);
                                          
                                          $timeout(function () {
                                              $scope.reloadView();
                                          }, 4000);
                                      } else {
                                          $scope.showRecoverBackupModal(response.url);
                                      }
                                  });
                      };
                      $scope.showRemoveBoardP = function (idboard) {
                        $scope.idboardP=idboard;
                        console.log("DSaf")
                        $('#ConfirmRemoveBoardP').modal({backdrop: 'static'})
                      };
                      $scope.RemoveBoardP=function(idboard){
                        $scope.viewActived=false;
                        var postdata = {id: idboard};
                        var URL = $scope.baseurl + "Board/getPrimaryBoardP";
                        $http.post(URL, postdata).success(function (response){
                          var post = {id: response.idboard};
                          console.log(postdata)
                          var URL1 = $scope.baseurl + "Board/removeBoard";
                          $http.post(URL1, post).success(function (res){
                            $route.reload();
                          });
                        });
                      }
                      $scope.showparcialBackup=function(images,voc,folder,cfg,panelb,foldername){
                          $scope.images=images;
                          $scope.voc=voc;
                          $scope.folder=folder;
                          $scope.cfg=cfg;
                          $scope.panelb=panelb;
                          $scope.foldername=foldername;
                          
                          if (images || voc || folder || cfg || panelb) {
                              $('#RecoverBackupModal').modal('hide');
                              $('#recmbackup').modal('toggle');
                          }
                          else {
                              $scope.nocheckbox = true;
                          }
                          
                      }
                      $scope.ShowAddGroupsInfo=function(){
                        $('#AddGroupsInfo').modal('toggle');
                      }
                      $scope.AddBoards=function(){
                          
                        $scope.viewActived=false;
                          
                        $http.get("Board/AddBoards").success(function (results) {
                                console.log(results.data);
                                $cookies.put('alreadyExamplePanels', true);
                                $scope.alreadyExamplePanels = true;
                                $route.reload();
                            });
                      }
        });
