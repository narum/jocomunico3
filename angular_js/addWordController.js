angular.module('controllers')
        .controller('addWordCtrl', function ($scope, $rootScope, txtContent, $location, $http, ngDialog, dropdownMenuBarInit, AuthService, Resources, $timeout) {
            txtContent("addWord").then(function (results) {
                $scope.content = results.data;
                $scope.initAddWordtest();
            });
            txtContent("searchArasaacImgs").then(function (results) {
                $scope.searchArasaacImgsContent = results.data;
            });
            
            //Dropdown Menu Bar
            $rootScope.dropdownMenuBar = null;
            $scope.shwimg=true;
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
            //Log Out Modal
            $scope.img = [];
            $scope.img.lowSorpresaFlecha = '/img/srcWeb/Mus/lowSorpresaFlecha.png';
            Resources.main.get({'section': 'logoutModal', 'idLanguage': $rootScope.interfaceLanguageId}, {'funct': "content"}).$promise
                    .then(function (results) {
                        $scope.logoutContent = results.data;
                    });
            $scope.logout = function () {
                $timeout(function () {
                    AuthService.logout();
                }, 1000);
            };

            $scope.initAddWord = function () {
              $scope.imgHasBeenUploaded = false;
              switch ($scope.addWordType)
                  {
                    case "name":
                        $scope.objAdd = {type: "name", nomtext: null, mf: false, singpl: false, contabincontab: null, determinat: "1", ispropernoun: false, defaultverb: null, plural: null, femeni: null, fempl: null, imgPicto: '/img/srcWeb/imagenesvarias/arrow-question.png', imgFolder: 'img/pictos/', supExp: true};
                        $scope.switchName = {s1: false, s2: false, s3: false, s4: false, s5: false, s6: false};
                        $scope.NClassList = [];
                        $scope.errAdd = {erradd1: false, erradd2: false,erradd3: false};
                        $scope.classNoun = [{classType: "animate", numType: 1, nameType: $scope.content.classname1},
                            {classType: "human", numType: 2, nameType: $scope.content.classname2},
                            {classType: "pronoun", numType: 3, nameType: $scope.content.classname3},
                            {classType: "animal", numType: 4, nameType: $scope.content.classname4},
                            {classType: "planta", numType: 5, nameType: $scope.content.classname5},
                            {classType: "vehicle", numType: 6, nameType: $scope.content.classname6},
                            {classType: "event", numType: 7, nameType: $scope.content.classname7},
                            {classType: "inanimate", numType: 8, nameType: $scope.content.classname8},
                            {classType: "objecte", numType: 9, nameType: $scope.content.classname9},
                            {classType: "color", numType: 10, nameType: $scope.content.classname10},
                            {classType: "forma", numType: 11, nameType: $scope.content.classname11},
                            {classType: "joc", numType: 12, nameType: $scope.content.classname12},
                            {classType: "cos", numType: 13, nameType: $scope.content.classname13},
                            {classType: "abstracte", numType: 14, nameType: $scope.content.classname14},
                            {classType: "lloc", numType: 15, nameType: $scope.content.classname15},
                            {classType: "menjar", numType: 16, nameType: $scope.content.classname16},
                            {classType: "beguda", numType: 17, nameType: $scope.content.classname17},
                            {classType: "time", numType: 18, nameType: $scope.content.classname18},
                            {classType: "hora", numType: 19, nameType: $scope.content.classname19},
                            {classType: "month", numType: 20, nameType: $scope.content.classname20},
                            {classType: "week", numType: 21, nameType: $scope.content.classname21},
                            {classType: "tool", numType: 22, nameType: $scope.content.classname22},
                            {classType: "profession", numType: 23, nameType: $scope.content.classname23},
                            {classType: "material", numType: 24, nameType: $scope.content.classname24}];


                        break;
                    case "adj":
                        $scope.objAdd = {type: "adj", masc: null, fem: null, mascpl: null, fempl: null,defaultverb: false, subjdef: false, imgPicto: '/img/srcWeb/imagenesvarias/arrow-question.png', imgFolder: 'img/pictos/', supExp: true};
                        $scope.switchAdj = {s1: false, s2: false, s3: false, s4: false, s5: false, s6: false};
                        $scope.AdjClassList = [];
                        $scope.errAdd = {erradd1: false, erradd2: false,erradd3: false};
                        $scope.classAdj = [{classType: "all", numType: 0, adjType: $scope.content.classadj0},
                                            {classType: "color", numType: 1, adjType: $scope.content.classadj1},
                                            {classType: "human", numType: 2, adjType: $scope.content.classadj2},
                                            {classType: "animate", numType: 3, adjType: $scope.content.classadj3},
                                            {classType: "objecte", numType: 4, adjType: $scope.content.classadj4},
                                            {classType: "menjar", numType: 5, adjType: $scope.content.classadj5},
                                            {classType: "ordinal", numType: 6, adjType: $scope.content.classadj6},
                                            {classType: "numero", numType: 7, adjType: $scope.content.classadj7}
                            ];

                        break;
                    default:
                        break;
                }
            };
            $scope.initAddWordtest = function () {

                if ($rootScope.addWordparam != null) {
                    $scope.NewModif = $rootScope.addWordparam.newmod;
                    $scope.addWordType = $rootScope.addWordparam.type;
                    $rootScope.addWordparam = null;
                    console.log($scope.addWordType);
                } else {
                    $location.path("/panelGroups");
                }

                var postdata = {idusu: $rootScope.userId, langabbr: $rootScope.languageAbbr, idlang: $rootScope.interfaceLanguageId};
                var URL = $scope.baseurl + "AddWord/getAllVerbs";
              
                $http.post(URL, postdata).
                    success(function (response)
                    {
                    $scope.verbsList = response.data;
                    if ($scope.NewModif == 0) {
                        $scope.idEditWord = $scope.addWordType;
                        var postdata = {id: $scope.idEditWord};
                        var URL = $scope.baseurl + "AddWord/EditWordType";
                        $http.post(URL, postdata).
                                success(function (response)
                                {
                                    $scope.addWordType = response.data[0].type;
                                    $scope.initAddWord();
                                    $scope.editWordData();
                                });
                    }
                    else{
                        $scope.initAddWord();
                    }

                    });
            };
            $scope.editWordData = function () {
                var postdata = {id: $scope.idEditWord, type: $scope.addWordType, idusu: $rootScope.userId, langabbr: $rootScope.languageAbbr};
                console.log(postdata);
                var URL = $scope.baseurl + "AddWord/EditWordGetData";
                $http.post(URL, postdata).
                        success(function (response)
                        {
                            $scope.imgHasBeenUploaded = true;
                            $scope.addWordEditData = response.data[0];
                            $scope.addWordEditData.imgPicto=response.data[0].imgPicto;
                            $scope.addWordEditData.imgFolder=response.data[0].imgFolder;
                            console.log($scope.addWordEditData.imgPicto);

                            var postdata = {id: $scope.idEditWord, type: $scope.addWordType, langabbr: $rootScope.languageAbbr};
                            URL = $scope.baseurl + "AddWord/EditWordGetClass";
                            $http.post(URL, postdata).
                                    success(function (response)
                                    {
                                        switch ($scope.addWordType)
                                        {
                                            case "name":
                                                console.log(response.data);
                                                if (response.data){
                                                    for(i = 0; i < response.data.length;i++){
                                                        $scope.addNClass(response.data[i].class);
                                                    }
                                                }
                                                $scope.objAdd = {type: "name", nomtext: $scope.addWordEditData.nomtext, mf: $scope.addWordEditData.mf == "masc" ? false : true,
                                                    singpl: $scope.addWordEditData.singpl == "sing" ? false : true, contabincontab: $scope.addWordEditData.contabincontab == "incontable" ? true : false,
                                                    determinat: $scope.addWordEditData.determinat, ispropernoun: $scope.addWordEditData.ispropernoun == 1 ? true : false,
                                                    defaultverb: $scope.addWordEditData.defaultverb == "0" ? null : $scope.addWordEditData.defaultverb, plural: $scope.addWordEditData.plural,
                                                    femeni: $scope.addWordEditData.femeni, fempl: $scope.addWordEditData.fempl, imgPicto: $scope.addWordEditData.imgPicto,
                                                    imgFolder: $scope.addWordEditData.imgFolder, supExp: $scope.addWordEditData.supportsExpansion == "1" ? true : false};
                                                $scope.switchName = {s1: false, s2: $scope.objAdd.femeni != null ? true : false, s3: $scope.objAdd.plural != null ? true : false,
                                                    s4: $scope.objAdd.fempl != null ? true : false, s5: $scope.objAdd.defaultverb != null ? true : false, s6: false};
                                                break;
                                            case "adj":
                                                console.log(response.data);
                                                if (response.data){
                                                    for(i = 0; i < response.data.length;i++){
                                                        $scope.addAdjClass(response.data[i].class);
                                                    }
                                                }
                                                $scope.objAdd = {type: "adj", masc: $scope.addWordEditData.masc, fem: $scope.addWordEditData.fem,
                                                    mascpl: $scope.addWordEditData.mascpl, fempl: $scope.addWordEditData.fempl,
                                                    defaultverb: $scope.addWordEditData.defaultverb == "86" ? false : true, subjdef: $scope.addWordEditData.subjdef == "1" ? false : true,
                                                    imgPicto: $scope.addWordEditData.imgPicto, imgFolder: $scope.addWordEditData.imgFolder,
                                                    supExp: $scope.addWordEditData.supportsExpansion == "1" ? true : false};
                                                $scope.switchAdj = {s1: $scope.addWordEditData.defaultverb, s2: $scope.addWordEditData.subjdef, s3: false, s4: false, s5: false, s6: false};
                                                break;
                                            default:
                                                break;
                                        }
                                    });
                        });
            };
            $scope.cancelAddWord = function () {
                $location.path("/panelGroups");
            };
            $scope.EditWordRemove = function () {
                var postdata = {id: $scope.idEditWord, type: $scope.addWordType, idusu: $rootScope.userId, langid: $rootScope.expanLanguageId};
                console.log(postdata);
                var URL = $scope.baseurl + "AddWord/EditWordRemove";
                $http.post(URL, postdata).
                        success(function (response)
                        {

                        });
                $location.path("/panelGroups");
            };
            $scope.saveAddWord = function () {
                $scope.commit = 1;
                switch ($scope.addWordType)
                {
                    case "name":
                        $scope.errAdd = {erradd1: false, erradd2: false,erradd3: false};
                        if($scope.objAdd.nomtext == null){
                            $scope.commit = 0;
                            $scope.errAdd.erradd1 = true;
                        }
                        if($scope.NClassList.length < 1 && $scope.objAdd.supExp){
                            $scope.commit = 0;
                            $scope.errAdd.erradd2 = true;
                        }
                        if($scope.objAdd.imgPicto == $scope.baseurl+'/img/srcWeb/imagenesvarias/arrow-question.png' || $scope.objAdd.imgPicto == null){
                            $scope.commit = 0;
                            $scope.errAdd.erradd3 = true;
                        }

                        if($scope.commit == 1)
                        {
                            console.log("Add NAME");
                            console.log($scope.objAdd.supExp);
                            
                            $scope.objAdd = {
                                type: "name",
                                nomtext: $scope.objAdd.nomtext,
                                mf: $scope.objAdd.mf == false ? "masc" : "fem",
                                singpl: $scope.objAdd.singpl == false ? "sing" : "pl",
                                contabincontab: $scope.objAdd.contabincontab == true ? "incontable" : "contable",
                                determinat: $scope.objAdd.determinat,
                                ispropernoun: $scope.objAdd.ispropernoun == true ? "1" : "0",
                                defaultverb: $scope.objAdd.defaultverb == null ? "0" : $scope.objAdd.defaultverb,
                                plural: $scope.switchName.s3 == false ? $scope.objAdd.nomtext : $scope.objAdd.plural,
                                femeni: $scope.switchName.s2 == false ? null : $scope.objAdd.femeni,
                                fempl: $scope.switchName.s4 == false ? null : $scope.objAdd.fempl,
                                imgPicto: $scope.objAdd.imgPicto,
                                imgFolder: $scope.objAdd.imgFolder,
                                pictoid: $scope.idEditWord != null ? $scope.idEditWord : false,
                                new: $scope.NewModif == 1 ? true : false,
                                class: $scope.NClassList,
                                supExp: $scope.objAdd.supExp == true ? "1" : "0"};
                            
                            console.log($scope.objAdd);
                            
                            if ($scope.objAdd.singpl == "pl"){
                                $scope.objAdd.plural = $scope.objAdd.nomtext;
                                $scope.objAdd.femeni = null;
                                $scope.objAdd.fempl = null;
                            }
                            if ($scope.objAdd.mf == "fem"){
                                $scope.objAdd.femeni = null;
                                $scope.objAdd.fempl = null;
                            }
                            if ($scope.objAdd.plural == null) {
                                $scope.objAdd.plural = $scope.objAdd.nomtext;
                            }
                        var URL = $scope.baseurl + "AddWord/InsertWordData";
                        var postdata = {objAdd: $scope.objAdd, idusu: $rootScope.userId, langabbr: $rootScope.languageAbbr};
                            $http.post(URL, postdata).success(function (response)
                            {

                            });
                            $location.path("/panelGroups");
                        }
                        break;
                    case "adj":
                        $scope.errAdd = {erradd1: false, erradd2: false,erradd3: false};
                        if($scope.objAdd.masc == null || $scope.objAdd.fem == null || $scope.objAdd.mascpl == null || $scope.objAdd.fempl == null){
                            $scope.commit = 0;
                            $scope.errAdd.erradd1 = true;
                        }
                        if($scope.AdjClassList.length < 1 && $scope.objAdd.supExp){
                            $scope.commit = 0;
                            $scope.errAdd.erradd2 = true;
                        }
                        if($scope.objAdd.imgPicto == '/img/srcWeb/imagenesvarias/arrow-question.png' || $scope.objAdd.imgPicto == null){
                            $scope.commit = 0;
                            $scope.errAdd.erradd3 = true;
                        }

                        if($scope.commit == 1)
                        {
                            console.log("Add ADJ");
                            console.log($scope.objAdd.supExp);
                            
                            $scope.objAdd = {type: "adj", masc: $scope.objAdd.masc, fem: $scope.objAdd.fem, mascpl: $scope.objAdd.mascpl,
                            fempl: $scope.objAdd.fempl, defaultverb: $scope.switchAdj.s1 == false ? "86" : "100", subjdef: $scope.switchAdj.s2 == false ? "1" : "3",
                            imgPicto: $scope.objAdd.imgPicto, imgFolder: $scope.objAdd.imgFolder, pictoid: $scope.idEditWord != null ? $scope.idEditWord : false, 
                            new: $scope.NewModif == 1 ? true : false, class: $scope.AdjClassList, 
                            supExp: $scope.objAdd.supExp == true ? "1" : "0"};
                        
                            console.log($scope.objAdd);
                            
                            var URL = $scope.baseurl + "AddWord/InsertWordData";
                            var postdata = {objAdd: $scope.objAdd, idusu: $rootScope.userId, langabbr: $rootScope.languageAbbr};
                            $http.post(URL, postdata).success(function (response)
                            {

                            });
                            $location.path("/panelGroups");
                        }
                        break;
                    default:
                        break;
                }



            };
            $scope.uploadFileToWord = function () {
                $scope.myFile = document.getElementById('file-input').files;
                $scope.uploading = true;
                var i;
                var uploadUrl = $scope.baseurl + "ImgUploader/upload";
                var fd = new FormData();
                fd.append('vocabulary', angular.toJson(true));
                fd.append('idusu', $rootScope.userId);
                for (i = 0; i < $scope.myFile.length; i++) {
                    fd.append('file' + i, $scope.myFile[i]);
                }
                $http.post(uploadUrl, fd,{
                    headers: {'Content-Type': undefined}
                })
                        .success(function (response) {
                            $scope.uploading = false;
                            $scope.objAdd.imgPicto = response.url;
                            $scope.imgFolder = 'img/pictos/';
                            $scope.shwimg=true;
                            if (response.error) {
                                console.log(response.errorText);
                                $scope.errorText = response.errorText;
                                $('#errorImgModal').modal({backdrop: 'static'});
                            }

                            else {
                              $scope.imgHasBeenUploaded = true;
                            }
                        })
                        .error(function (response) {
                        });
            };
            $scope.addNClass = function (nameTypeClass) {
                angular.forEach($scope.classNoun, function (value, key) {
                    if ((value.nameType.toUpperCase() == nameTypeClass.toUpperCase()) ||
                            (value.classType.toUpperCase() == nameTypeClass.toUpperCase())) {
                        $scope.NClassList.push($scope.classNoun[key]);
                        $scope.classNoun.splice(key, 1);
                    }
                });
            };
            $scope.removeNounclass = function (index) {
                $scope.classNoun.push($scope.NClassList[index]);
                $scope.NClassList.splice(index, 1);
            };
            $scope.addAdjClass = function (AdjTypeClass) {
                angular.forEach($scope.classAdj, function (value, key) {
                    if ((value.adjType.toUpperCase() == AdjTypeClass.toUpperCase()) ||
                            (value.classType.toUpperCase() == AdjTypeClass.toUpperCase())) {
                        $scope.AdjClassList.push($scope.classAdj[key]);
                        $scope.classAdj.splice(key, 1);
                    }
                });
            };
            $scope.removeAdjclass = function (index) {
                $scope.classAdj.push($scope.AdjClassList[index]);
                $scope.AdjClassList.splice(index, 1);
            };

            $scope.img = [];
            $scope.img.Patterns1_08 = '/img/srcWeb/patterns/pattern3.png';
            $scope.style_changes_title = '';

             // Activate information modals (popups)
            $scope.toggleInfoModal = function (title, text) {
                $scope.infoModalContent = text;
                $scope.infoModalTitle = title;
                $scope.style_changes_title = 'padding-top: 2vh;';
                $('#infoModal').modal('toggle');
            };
            
             // Open popup to search personal images
            $scope.popUpSearchImgs = function(){
                console.log("IN?");
                $('#imgsSearchModal').modal('toggle');//Show modal
            };
            
            $scope.searchImg = function (name)
            {
                $timeout.cancel($scope.searchTimeout);
                $scope.searchTimeout = $timeout(function () {
                    $scope.searchStartImg(name);
                }, 500);
            };

            /*
             * Return the ARASAAC IMGS from the ARASAAC server. 
             * There are two types of search in color or in black & white
             */
             $scope.searchStartImg = function (name) {
                var url = $scope.baseurl + "ARASAAC/searchDelete";
                var postdata = {name: name, idusu: $rootScope.userId};
                $http.post(url, postdata).
                success(function (response)
                {
                    $scope.imgData = response.data;
                    // console.log(response.data);
                });
             };
             
             $scope.selectImage = function (path) {
                 aux = path.split("/");
                 $scope.objAdd.imgFolder = aux[0] + "/" + aux[1] + "/";
                 $scope.objAdd.imgPicto = aux[2];
                 $scope.imgHasBeenUploaded = true;
                 $('#imgsSearchModal').modal('hide'); //Hide modal
             };

        });
