var app = angular.module('controllers');
    app.controller('addVerb', function ($scope, $rootScope, txtContent, $location, $http, $interval, ngDialog, dropdownMenuBarInit, AuthService, Resources, $timeout) {
        // Comprobación del login   IMPORTANTE!!! PONER EN TODOS LOS CONTROLADORES
        if (!$rootScope.isLogged) {
            $location.path('/home');
            $rootScope.dropdownMenuBarValue = '/home'; //Dropdown bar button selected on this view
        }
        // Pedimos los textos para cargar la pagina
        var textBD = txtContent("addVerb").then(function (results) {return results.data});

        txtContent("addVerb").then(function (results) {
            $scope.content = results.data;
            console.log(results.data.infoVerb);
            $scope.editMode();
        });
        txtContent("searchArasaacImgs").then(function (results) {
            $scope.searchArasaacImgsContent = results.data;
        });
        
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

        //Rebuild Scrollbar inside timeout
        $timeout(function () {
            $scope.$broadcast('rebuild:conjugationScrollbar');
        });

        $timeout(function () {
            $scope.$broadcast('rebuild:verbPatternScrollbar');
        });

        $scope.rebuildScrollBarPatterns = function () {
            $scope.$broadcast('rebuild:verbPatternScrollbar');
        };
        
        $scope.rebuildScrollBarConjs = function () {
            $scope.$broadcast('rebuild:conjugationScrollbar');
        };
        
        $scope.imgHasBeenUploaded = false;
        $scope.imgPicto = {value: 'arrow-question.png'};
        $scope.imgFolder = {value: 'img/pictos/'};
        $scope.verb = {name: ''};
        $scope.pronominal = {value: 0};

        $scope.borrarImperativo = function(value){
            if(value === 1){
                $scope.imperativo = {name:'imperatiu', persona:{ps2:'', ps3:'', pp1:'', pp2:'', pp3:''}};
            }
        };

        $scope.addPronombres = function(value){
            if(value === 1){
                setPronombre($scope.presente);
                setPronombre($scope.imperfecto);
                setPronombre($scope.pasado);
                setPronombre($scope.futuro);
                setPronombre($scope.prsubj);
                setPronombre($scope.impsubj);
                $scope.formasNoPersonales = {infinitivo: '', gerundio: '', participio: ''};
            } else {
                $scope.presente = new conjugation('present');
                $scope.imperfecto = new conjugation('imperfecte');
                $scope.pasado = new conjugation('passat');
                $scope.futuro = new conjugation('futur');
                $scope.prsubj = new conjugation('prsubj');
                $scope.impsubj = new conjugation('impsubj');
                $scope.imperativo = {name:'imperatiu', persona:{ps2:'', ps3:'', pp1:'', pp2:'', pp3:''}};
                $scope.formasNoPersonales = {infinitivo: '', gerundio: '', participio: ''};
            }
        };

        function setPronombre(Conjugation){
            Conjugation.persona.ps1 = $scope.pronombres[0];
            Conjugation.persona.ps2 = $scope.pronombres[1];
            Conjugation.persona.ps3 = $scope.pronombres[2];
            Conjugation.persona.pp1 = $scope.pronombres[3];
            Conjugation.persona.pp2 = $scope.pronombres[4];
            Conjugation.persona.pp3 = $scope.pronombres[5];
        }

        function persona (){
            this.ps1 = '';
            this.ps2 = '';
            this.ps3 = '';
            this.pp1 = '';
            this.pp2 = '';
            this.pp3 = '';
        };
        function conjugation (name){
            this.name = name;
            this.persona = new persona();
        };

        $scope.presente = new conjugation('present');
        $scope.imperfecto = new conjugation('imperfecte');
        $scope.pasado = new conjugation('passat');
        $scope.futuro = new conjugation('futur');
        $scope.prsubj = new conjugation('prsubj');
        $scope.impsubj = new conjugation('impsubj');
        $scope.imperativo = {name:'imperatiu', persona:{ps2:'', ps3:'', pp1:'', pp2:'', pp3:''}};
        $scope.formasNoPersonales = {infinitivo: '', gerundio: '', participio: ''};

        $scope.cancelAddVerb = function () {
            $location.path("/panelGroups");
        };

        $scope.uploadFileToWord = function () {
            $scope.myFile = document.getElementById('file-input').files;
            $scope.uploading = true;
            var i;
            var uploadUrl = $scope.baseurl + "ImgUploader/upload";
            var fd = new FormData();
            fd.append('vocabulary', angular.toJson(true));
            for (i = 0; i < $scope.myFile.length; i++) {
                fd.append('file' + i, $scope.myFile[i]);
            }
            $http.post(uploadUrl, fd,{
                headers: {'Content-Type': undefined}
            })
                .success(function (response) {
                    $scope.uploading = false;
                    var imgPicto = response.url;
                    $scope.imgPicto.value = imgPicto;
                    $scope.imgFolder.value = 'img/pictos/';
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
        $scope.verbConjugations;

        $scope.getConjugations = function (){
            var URL = $scope.baseurl + "AddVerb/getConjugations";
            var postdata = {verb : $scope.verb.name};
            console.log(postdata);
            $http.post(URL, postdata).success(function (response) {
                console.log(response);
                $scope.conjugations = response;
                setConjugations();
            });
        };
        function setConjugations(){
            setPresente();
            setImperfecto();
            setPasado();
            setFuturo();
            setPrsubj();
            setImpsubj();
            setImperativo();
            setFormasNoPersonales();
        }
        function setPresente(){
            if($scope.pronominal.value === 1){
                $scope.presente.persona.ps1 = $scope.presente.persona.ps1+$scope.conjugations.presente.ps1;
                $scope.presente.persona.ps2 = $scope.presente.persona.ps2+$scope.conjugations.presente.ps2;
                $scope.presente.persona.ps3 = $scope.presente.persona.ps3+$scope.conjugations.presente.ps3;
                $scope.presente.persona.pp1 = $scope.presente.persona.pp1+$scope.conjugations.presente.pp1;
                $scope.presente.persona.pp2 = $scope.presente.persona.pp2+$scope.conjugations.presente.pp2;
                $scope.presente.persona.pp3 = $scope.presente.persona.pp3+$scope.conjugations.presente.pp3;
            }else {
                $scope.presente.persona.ps1 = $scope.conjugations.presente.ps1;
                $scope.presente.persona.ps2 = $scope.conjugations.presente.ps2;
                $scope.presente.persona.ps3 = $scope.conjugations.presente.ps3;
                $scope.presente.persona.pp1 = $scope.conjugations.presente.pp1;
                $scope.presente.persona.pp2 = $scope.conjugations.presente.pp2;
                $scope.presente.persona.pp3 = $scope.conjugations.presente.pp3;
            }
        }
        function setImperfecto(){
            if($scope.pronominal.value === 1){
                $scope.imperfecto.persona.ps1 = $scope.imperfecto.persona.ps1+$scope.conjugations.imperfecto.ps1;
                $scope.imperfecto.persona.ps2 = $scope.imperfecto.persona.ps2+$scope.conjugations.imperfecto.ps2;
                $scope.imperfecto.persona.ps3 = $scope.imperfecto.persona.ps3+$scope.conjugations.imperfecto.ps3;
                $scope.imperfecto.persona.pp1 = $scope.imperfecto.persona.pp1+$scope.conjugations.imperfecto.pp1;
                $scope.imperfecto.persona.pp2 = $scope.imperfecto.persona.pp2+$scope.conjugations.imperfecto.pp2;
                $scope.imperfecto.persona.pp3 = $scope.imperfecto.persona.pp3+$scope.conjugations.imperfecto.pp3;
            }
            else{
                $scope.imperfecto.persona.ps1 = $scope.conjugations.imperfecto.ps1;
                $scope.imperfecto.persona.ps2 = $scope.conjugations.imperfecto.ps2;
                $scope.imperfecto.persona.ps3 = $scope.conjugations.imperfecto.ps3;
                $scope.imperfecto.persona.pp1 = $scope.conjugations.imperfecto.pp1;
                $scope.imperfecto.persona.pp2 = $scope.conjugations.imperfecto.pp2;
                $scope.imperfecto.persona.pp3 = $scope.conjugations.imperfecto.pp3;
            }
        }
        function setPasado(){
            if($scope.pronominal.value === 1){
                $scope.pasado.persona.ps1 = $scope.pasado.persona.ps1+$scope.conjugations.pasado.ps1;
                $scope.pasado.persona.ps2 = $scope.pasado.persona.ps2+$scope.conjugations.pasado.ps2;
                $scope.pasado.persona.ps3 = $scope.pasado.persona.ps3+$scope.conjugations.pasado.ps3;
                $scope.pasado.persona.pp1 = $scope.pasado.persona.pp1+$scope.conjugations.pasado.pp1;
                $scope.pasado.persona.pp2 = $scope.pasado.persona.pp2+$scope.conjugations.pasado.pp2;
                $scope.pasado.persona.pp3 = $scope.pasado.persona.pp3+$scope.conjugations.pasado.pp3;
            }
            else{
                $scope.pasado.persona.ps1 = $scope.conjugations.pasado.ps1;
                $scope.pasado.persona.ps2 = $scope.conjugations.pasado.ps2;
                $scope.pasado.persona.ps3 = $scope.conjugations.pasado.ps3;
                $scope.pasado.persona.pp1 = $scope.conjugations.pasado.pp1;
                $scope.pasado.persona.pp2 = $scope.conjugations.pasado.pp2;
                $scope.pasado.persona.pp3 = $scope.conjugations.pasado.pp3;
            }
        }
        function setFuturo(){
            if($scope.pronominal.value === 1){
                $scope.futuro.persona.ps1 = $scope.futuro.persona.ps1+$scope.conjugations.futuro.ps1;
                $scope.futuro.persona.ps2 = $scope.futuro.persona.ps2+$scope.conjugations.futuro.ps2;
                $scope.futuro.persona.ps3 = $scope.futuro.persona.ps3+$scope.conjugations.futuro.ps3;
                $scope.futuro.persona.pp1 = $scope.futuro.persona.pp1+$scope.conjugations.futuro.pp1;
                $scope.futuro.persona.pp2 = $scope.futuro.persona.pp2+$scope.conjugations.futuro.pp2;
                $scope.futuro.persona.pp3 = $scope.futuro.persona.pp3+$scope.conjugations.futuro.pp3;
            }
            else{
                $scope.futuro.persona.ps1 = $scope.conjugations.futuro.ps1;
                $scope.futuro.persona.ps2 = $scope.conjugations.futuro.ps2;
                $scope.futuro.persona.ps3 = $scope.conjugations.futuro.ps3;
                $scope.futuro.persona.pp1 = $scope.conjugations.futuro.pp1;
                $scope.futuro.persona.pp2 = $scope.conjugations.futuro.pp2;
                $scope.futuro.persona.pp3 = $scope.conjugations.futuro.pp3;
            }
        }
        function setPrsubj(){
            if($scope.pronominal.value === 1){
                $scope.prsubj.persona.ps1 = $scope.prsubj.persona.ps1+$scope.conjugations.prsubj.ps1;
                $scope.prsubj.persona.ps2 = $scope.prsubj.persona.ps2+$scope.conjugations.prsubj.ps2;
                $scope.prsubj.persona.ps3 = $scope.prsubj.persona.ps3+$scope.conjugations.prsubj.ps3;
                $scope.prsubj.persona.pp1 = $scope.prsubj.persona.pp1+$scope.conjugations.prsubj.pp1;
                $scope.prsubj.persona.pp2 = $scope.prsubj.persona.pp2+$scope.conjugations.prsubj.pp2;
                $scope.prsubj.persona.pp3 = $scope.prsubj.persona.pp3+$scope.conjugations.prsubj.pp3;
            }
            else{
                $scope.prsubj.persona.ps1 = $scope.conjugations.prsubj.ps1;
                $scope.prsubj.persona.ps2 = $scope.conjugations.prsubj.ps2;
                $scope.prsubj.persona.ps3 = $scope.conjugations.prsubj.ps3;
                $scope.prsubj.persona.pp1 = $scope.conjugations.prsubj.pp1;
                $scope.prsubj.persona.pp2 = $scope.conjugations.prsubj.pp2;
                $scope.prsubj.persona.pp3 = $scope.conjugations.prsubj.pp3;
            }
        }
        function setImpsubj(){
            if($scope.pronominal.value === 1){
                $scope.impsubj.persona.ps1 = $scope.impsubj.persona.ps1+$scope.conjugations.impsubj.ps1;
                $scope.impsubj.persona.ps2 = $scope.impsubj.persona.ps2+$scope.conjugations.impsubj.ps2;
                $scope.impsubj.persona.ps3 = $scope.impsubj.persona.ps3+$scope.conjugations.impsubj.ps3;
                $scope.impsubj.persona.pp1 = $scope.impsubj.persona.pp1+$scope.conjugations.impsubj.pp1;
                $scope.impsubj.persona.pp2 = $scope.impsubj.persona.pp2+$scope.conjugations.impsubj.pp2;
                $scope.impsubj.persona.pp3 = $scope.impsubj.persona.pp3+$scope.conjugations.impsubj.pp3;
            }
            else{
                $scope.impsubj.persona.ps1 = $scope.conjugations.impsubj.ps1;
                $scope.impsubj.persona.ps2 = $scope.conjugations.impsubj.ps2;
                $scope.impsubj.persona.ps3 = $scope.conjugations.impsubj.ps3;
                $scope.impsubj.persona.pp1 = $scope.conjugations.impsubj.pp1;
                $scope.impsubj.persona.pp2 = $scope.conjugations.impsubj.pp2;
                $scope.impsubj.persona.pp3 = $scope.conjugations.impsubj.pp3;
            }
        }
        function setImperativo(){
            var pronominal = $scope.pronominal.value;
            if(pronominal === 0) {
                $scope.imperativo.persona.ps2 = $scope.conjugations.imperativo.ps2;
                $scope.imperativo.persona.ps3 = $scope.conjugations.imperativo.ps3;
                $scope.imperativo.persona.pp1 = $scope.conjugations.imperativo.pp1;
                $scope.imperativo.persona.pp2 = $scope.conjugations.imperativo.pp2;
                $scope.imperativo.persona.pp3 = $scope.conjugations.imperativo.pp3;
            }else if(pronominal === 1){
                $scope.imperativo = {name:'imperatiu', persona:{ps2:'', ps3:'', pp1:'', pp2:'', pp3:''}};
            }
        }
        function setFormasNoPersonales(){
            $scope.formasNoPersonales.infinitivo = $scope.conjugations.infinitivo;
            $scope.formasNoPersonales.gerundio = $scope.conjugations.gerundio;
            $scope.formasNoPersonales.participio = $scope.conjugations.participio;
        }

        //PATRONES VERBALES

        $scope.verbPattern1 = false;
        $scope.verbPattern2 = false;
        $scope.showVerbPattern2 = false;

        $scope.showPattern1 = {'CD':false,'Receiver':false,'Beneficiary':false,'Acomp':false,'Tool':false,'Modo':false,'Locto':false};
        $scope.showPattern2 = {'CD':false,'Receiver':false,'Beneficiary':false,'Acomp':false,'Tool':false,'Modo':false,'Locto':false};

        $scope.Pattern1 = {
            'Patron': {'pronominal': false, 'subj':"", 'subjdef':"", 'defaulttense':"", 'exemple':""},
            'CD': {'priority': 0, 'type':"", 'preposition':"", 'themedef': ""},
            'Receiver': {'priority':0, 'preposition':"", 'receiverdef':""},
            'Beneficiary': {'priority':0, 'type':"", 'preposition':""},
            'Acomp': {'priority':0, 'preposition':""},
            'Tool': {'priority':0, 'preposition':""},
            'Modo': {'priority':0, 'type':""},
            'Locto': {'priority':0, 'type':"", 'preposition':""}
        };
        $scope.Pattern2 = {
            'Patron': {'pronominal': false, 'subj':"", 'subjdef':"", 'defaulttense':"", 'exemple':""},
            'CD': {'priority': 0, 'type':"", 'preposition':"", 'themedef': ""},
            'Receiver': {'priority':0, 'preposition':"", 'receiverdef':""},
            'Beneficiary': {'priority':0, 'type':"", 'preposition':""},
            'Acomp': {'priority':0, 'preposition':""},
            'Tool': {'priority':0, 'preposition':""},
            'Modo': {'priority':0, 'type':""},
            'Locto': {'priority':0, 'type':"", 'preposition':""}
        };

        $scope.insertPattern2 = function(){
            $scope.verbPattern2 = true;
            $scope.$broadcast('rebuild:verbPatternScrollbar');
        }
        $scope.deletePattern2 = function(){
            $scope.showVerbPattern2 = false;
            $scope.verbPattern2 = false;
            $scope.Pattern2 = {
                'Patron': {'pronominal': false, 'subj':"", 'subjdef':"", 'defaulttense':"", 'exemple':""},
                'CD': {'priority': 0, 'type':"", 'preposition':"", 'themedef':""},
                'Receiver': {'priority':0, 'preposition':"", 'receiverdef':""},
                'Beneficiary': {'priority':0, 'type':"", 'preposition':""},
                'Acomp': {'priority':0, 'preposition':""},
                'Tool': {'priority':0, 'preposition':""},
                'Modo': {'priority':0, 'type':""},
                'Locto': {'priority':0, 'type':"", 'preposition':""}
            };
            $scope.showPattern2 = {'CD':false,'Receiver':false,'Beneficiary':false,'Acomp':false,'Tool':false,'Modo':false,'Locto':false};
            $scope.$broadcast('rebuild:verbPatternScrollbar');
        };
        $scope.pronombres = function(){
            if ($scope.interfaceLanguageId == 1){//CAT
                return['em ', 'et ', 'es ', 'ens ', 'us ', 'es '];
            }else if($scope.interfaceLanguageId == 2){//ESP
                return['me ', 'te ', 'se ', 'nos ', 'os ', 'se '];
            }
        }();
        $scope.subjOptions = function(){
            if ($scope.interfaceLanguageId == 1){//CAT
                return [{value:"noun", name:"Nom", common:true, visible:true},{value:"animate" ,name:"Animat", common:true, visible:true},{value:"human", name:"Humà", common:true, visible:true},
                    {value:"pronoun", name:"Pronom", common:false, visible:true},{value:"animal", name:"Animal", common:false, visible:true},{value:"planta", name:"Planta", common:false, visible:true},
                    {value:"vehicle", name:"Vehicle", common:false, visible:true},{value:"event", name:"Esdeveniment", common:false, visible:true},{value:"inanimate", name:"Inanimat", common:false, visible:true},
                    {value:"objecte", name:"Objecte", common:true, visible:true},{value:"color", name:"Color", common:false, visible:true},{value:"forma", name:"Forma", common:false, visible:true},
                    {value:"joc", name:"Joc", common:false, visible:true},{value:"cos", name:"Cos", common:false, visible:true},{value:"abstracte", name:"Abstracte", common:false, visible:true},
                    {value:"lloc", name:"Lloc", common:false, visible:true},{value:"menjar", name:"Menjar", common:false, visible:true},{value:"beguda", name:"Beguda", common:false, visible:true},
                    {value:"hora", name:"Hora", common:false, visible:true},{value:"month", name:"Mes", common:false, visible:true},
                    {value: "week", name:"Setmana", common:false, visible:true},{value:"tool", name:"Eina", common:false, visible:true},{value:"profession", name:"Professió", common:false, visible:true},
                    {value:"material", name:"Material", common:false, visible:true}, {value:"verb", name:"Verb", common:true, visible:true}];
            }else if($scope.interfaceLanguageId == 2){//ESP
                return [{value:"noun", name:"Nombre", common:true, visible:true},{value:"animate" ,name:"Animado", common:true, visible:true},{value:"human", name:"Humano", common:true, visible:true},
                    {value:"pronoun", name:"Pronombre", common:false, visible:true},{value:"animal", name:"Animal", common:false, visible:true},{value:"planta", name:"Planta", common:false, visible:true},
                    {value:"vehicle", name:"Vehiculo", common:false, visible:true},{value:"event", name:"Evento", common:false, visible:true},{value:"inanimate", name:"Inanimado", common:false, visible:true},
                    {value:"objecte", name:"Objeto", common:true, visible:true},{value:"color", name:"Color", common:false, visible:true},{value:"forma", name:"Forma", common:false, visible:true},
                    {value:"joc", name:"Juego", common:false, visible:true},{value:"cos", name:"Cuerpo", common:false, visible:true},{value:"abstracte", name:"Abstracto", common:false, visible:true},
                    {value:"lloc", name:"Lugar", common:false, visible:true},{value:"menjar", name:"Comida", common:false, visible:true},{value:"beguda", name:"Bebida", common:false, visible:true},
                    {value:"hora", name:"Hora", common:false, visible:true},{value:"month", name:"Mes", common:false, visible:true},
                    {value: "week", name:"Semana", common:false, visible:true},{value:"tool", name:"Herramienta", common:false, visible:true},{value:"profession", name:"Profesión", common:false, visible:true},
                    {value:"material", name:"Material", common:false, visible:true}, {value:"verb", name:"Verbo", common:true, visible:true}];
            }
        }();

        $scope.CDOptionsPattern1 = function(){
            if ($scope.interfaceLanguageId == 1){//CAT
                return [{value:"noun", name:"Nom", common:true, visible:true},{value:"animate" ,name:"Animat", common:true, visible:true},{value:"human", name:"Huma", common:true, visible:true},
                        {value:"pronoun", name:"Pronom", common:false, visible:true},{value:"animal", name:"Animal", common:false, visible:true},{value:"planta", name:"Planta", common:false, visible:true},
                        {value:"vehicle", name:"Vehicle", common:false, visible:true},{value:"event", name:"Event", common:false, visible:true},{value:"inanimate", name:"Inanimat", common:false, visible:true},
                        {value:"objecte", name:"Objecte", common:true, visible:true},{value:"color", name:"Color", common:false, visible:true},{value:"forma", name:"Forma", common:false, visible:true},
                        {value:"joc", name:"Joc", common:false, visible:true},{value:"cos", name:"Cos", common:false, visible:true},{value:"abstracte", name:"Abstracte", common:false, visible:true},
                        {value:"lloc", name:"Lloc", common:false, visible:true},{value:"menjar", name:"Menjar", common:false, visible:true},{value:"beguda", name:"Beguda", common:false, visible:true},
                        {value:"hora", name:"Hora", common:false, visible:true},{value:"month", name:"Mes", common:false, visible:true},
                        {value: "week", name:"Setmana", common:false, visible:true},{value:"tool", name:"Eina", common:false, visible:true},{value:"profession", name:"Professió", common:false, visible:true},
                        {value:"material", name:"Material", common:false, visible:true}, {value:"verb", name:"Verb", common:true, visible:true}];
            }else if($scope.interfaceLanguageId == 2){//ESP
                return [{value:"noun", name:"Nombre", common:true, visible:true},{value:"animate" ,name:"Animado", common:true, visible:true},{value:"human", name:"Humano", common:true, visible:true},
                        {value:"pronoun", name:"Pronombre", common:false, visible:true},{value:"animal", name:"Animal", common:false, visible:true},{value:"planta", name:"Planta", common:false, visible:true},
                        {value:"vehicle", name:"Vehiculo", common:false, visible:true},{value:"event", name:"Evento", common:false, visible:true},{value:"inanimate", name:"Inanimado", common:false, visible:true},
                        {value:"objecte", name:"Objeto", common:true, visible:true},{value:"color", name:"Color", common:false, visible:true},{value:"forma", name:"Forma", common:false, visible:true},
                        {value:"joc", name:"Juego", common:false, visible:true},{value:"cos", name:"Cosa", common:false, visible:true},{value:"abstracte", name:"Abstracto", common:false, visible:true},
                        {value:"lloc", name:"Lugar", common:false, visible:true},{value:"menjar", name:"Comida", common:false, visible:true},{value:"beguda", name:"Bebida", common:false, visible:true},
                        {value:"hora", name:"Hora", common:false, visible:true},{value:"month", name:"Mes", common:false, visible:true},
                        {value: "week", name:"Semana", common:false, visible:true},{value:"tool", name:"Herramienta", common:false, visible:true},{value:"profession", name:"Profesión", common:false, visible:true},
                        {value:"material", name:"Material", common:false, visible:true}, {value:"verb", name:"Verbo", common:true, visible:true}];
            }
        }();

        $scope.CDOptionsPattern2 = function(){
            if ($scope.interfaceLanguageId == 1){//CAT
                return [{value:"noun", name:"Nom", common:true, visible:true},{value:"animate" ,name:"Animat", common:true, visible:true},{value:"human", name:"Huma", common:true, visible:true},
                    {value:"pronoun", name:"Pronom", common:false, visible:true},{value:"animal", name:"Animal", common:false, visible:true},{value:"planta", name:"Planta", common:false, visible:true},
                    {value:"vehicle", name:"Vehicle", common:false, visible:true},{value:"event", name:"Event", common:false, visible:true},{value:"inanimate", name:"Inanimat", common:false, visible:true},
                    {value:"objecte", name:"Objecte", common:true, visible:true},{value:"color", name:"Color", common:false, visible:true},{value:"forma", name:"Forma", common:false, visible:true},
                    {value:"joc", name:"Joc", common:false, visible:true},{value:"cos", name:"Cos", common:false, visible:true},{value:"abstracte", name:"Abstracte", common:false, visible:true},
                    {value:"lloc", name:"Lloc", common:false, visible:true},{value:"menjar", name:"Menjar", common:false, visible:true},{value:"beguda", name:"Beguda", common:false, visible:true},
                    {value:"hora", name:"Hora", common:false, visible:true},{value:"month", name:"Mes", common:false, visible:true},
                    {value: "week", name:"Setmana", common:false, visible:true},{value:"tool", name:"Eina", common:false, visible:true},{value:"profession", name:"Professió", common:false, visible:true},
                    {value:"material", name:"Material", common:false, visible:true}, {value:"verb", name:"Verb", common:true, visible:true}];
            }else if($scope.interfaceLanguageId == 2){//ESP
                return [{value:"noun", name:"Nombre", common:true, visible:true},{value:"animate" ,name:"Animado", common:true, visible:true},{value:"human", name:"Humano", common:true, visible:true},
                    {value:"pronoun", name:"Pronombre", common:false, visible:true},{value:"animal", name:"Animal", common:false, visible:true},{value:"planta", name:"Planta", common:false, visible:true},
                    {value:"vehicle", name:"Vehiculo", common:false, visible:true},{value:"event", name:"Evento", common:false, visible:true},{value:"inanimate", name:"Inanimado", common:false, visible:true},
                    {value:"objecte", name:"Objeto", common:true, visible:true},{value:"color", name:"Color", common:false, visible:true},{value:"forma", name:"Forma", common:false, visible:true},
                    {value:"joc", name:"Juego", common:false, visible:true},{value:"cos", name:"Cosa", common:false, visible:true},{value:"abstracte", name:"Abstracto", common:false, visible:true},
                    {value:"lloc", name:"Lugar", common:false, visible:true},{value:"menjar", name:"Comida", common:false, visible:true},{value:"beguda", name:"Bebida", common:false, visible:true},
                    {value:"hora", name:"Hora", common:false, visible:true},{value:"month", name:"Mes", common:false, visible:true},
                    {value: "week", name:"Semana", common:false, visible:true},{value:"tool", name:"Herramienta", common:false, visible:true},{value:"profession", name:"Profesión", common:false, visible:true},
                    {value:"material", name:"Material", common:false, visible:true}, {value:"verb", name:"Verbo", common:true, visible:true}];
            }
        }();

        $scope.defecto = function (){
            if ($scope.interfaceLanguageId == 1){
                return [{value:1, name:"jo"},{value:2, name:"tu"},{value:"una cosa", name:"una cosa"}]
            }else if($scope.interfaceLanguageId == 2){
                return [{value:1, name:"yo"},{value:2, name:"tú"},{value:"una cosa", name:"una cosa"}]
            }
        }();

        $scope.patternTemp = function (){
            if ($scope.interfaceLanguageId == 1){
                return [{value:"present", name:"Present"},{value:"perfet", name:"Passat immediat"},{value:"perifrastic", name:"Passat"},{value:"futur", name:"Futur"},{value:"imperatiu", name:"Imperatiu"}]
            }else if($scope.interfaceLanguageId == 2){
                return [{value:"present", name:"Presente"},{value:"perfet", name:"Pasado inmediato"},{value:"perifrastic", name:"Pasado"},{value:"futur", name:"Futuro"},{value:"imperatiu", name:"Imperativo"}]
            }
        }();

        $scope.complementos = function (){
            if ($scope.interfaceLanguageId == 1){
                return [{id:1, name:"Objecte directe"},{id:2, name:"Receptor"},{id:3, name:"Beneficiari"},
                    {id:4, name:"Acompanyant"}, {id: 5, name:"Eina"}, {id:6, name:"Complement de manera"}, {id:7, name:"Complement de lloc"}]
            }else if($scope.interfaceLanguageId == 2){
                return [{id:1, name:"Objeto directo"},{id:2, name:"Receptor"},{id:3, name:"Beneficiario"},
                        {id:4, name:"Acompañante"}, {id: 5, name:"Herramienta"}, {id:6, name:"Complemento de modo"}, {id:7, name:"Complemento de lugar"}]
            }
        }();
        
        $scope.themedefecto = function (){
            if ($scope.interfaceLanguageId == 1){
                return [{value:"", name:""},{value:"jo", name:"jo"},{value:"tu", name:"tu"},{value:"una cosa", name:"una cosa"},
                {value:"ho", name:"ho"}]
            }else if($scope.interfaceLanguageId == 2){
                return [{value:"", name:""},{value:"yo", name:"yo"},{value:"tú", name:"tú"},{value:"una cosa", name:"una cosa"},
                {value:"lo", name:"lo"}]
            }
        }();
        
        $scope.receiverdefecto = function (){
            if ($scope.interfaceLanguageId == 1){
                return [{value:"", name:""},{value:"mi", name:"jo"},{value:"tu", name:"tu"}]
            }else if($scope.interfaceLanguageId == 2){
                return [{value:"", name:""},{value:"mí", name:"yo"},{value:"tú", name:"tú"}]
            }
        }();

        $scope.themeOptions = function (){
            if ($scope.interfaceLanguageId == 1){
                return[{value:"opt", name:"Opcional"}, {value:"1", name:"Obligatori"}];
            }else if ($scope.interfaceLanguageId == 2){
                return[{value:"opt", name:"Opcional"}, {value:"1", name:"Obligatorio"}];
            }
        }();

        $scope.maneraOptions = function (){
            if ($scope.interfaceLanguageId == 1){
                return [{value:"adj", name:"Adjectiu"},{value:"adv", name:"Adverbi"},{value:"quant", name:"Quantificador"}]
            }else if($scope.interfaceLanguageId == 2){
                return [{value:"adj", name:"Adjetivo"},{value:"adv", name:"Adverbio"},{value:"quant", name:"Cuantificador"}]
            }
        }();

        $scope.BenefOptionsPattern1 = function (){
            var array = $scope.CDOptionsPattern1;
            if ($scope.interfaceLanguageId == 1){
                var b = [{value:"adv", name:"Adverbi", common:true, visible:true}]
            }else if ($scope.interfaceLanguageId == 2){
                var b = [{value:"adv", name:"Adverbio", common:true, visible:true}]
            }
            return array.concat(b);
        }();

        $scope.BenefOptionsPattern2 = function (){
            var array = $scope.CDOptionsPattern2;
            if ($scope.interfaceLanguageId == 1){
                var b = [{value:"adv", name:"Adverbi", common:true, visible:true}]
            }else if ($scope.interfaceLanguageId == 2){
                var b = [{value:"adv", name:"Adverbio", common:true, visible:true}]
            }
            return array.concat(b);
        }();

        $scope.LocatOptionsPattern1 = function (){
            var array = $scope.CDOptionsPattern1;
            if ($scope.interfaceLanguageId == 1){
                var b = [{value:"adv", name:"Adverbi", common:true, visible:true}]
            }else if ($scope.interfaceLanguageId == 2){
                var b = [{value:"adv", name:"Adverbio", common:true, visible:true}]
            }
            return array.concat(b);
        }();

        $scope.LocatOptionsPattern2 = function (){
            var array = $scope.CDOptionsPattern2;
            if ($scope.interfaceLanguageId == 1){
                var b = [{value:"adv", name:"Adverbi", common:true, visible:true}]
            }else if ($scope.interfaceLanguageId == 2){
                var b = [{value:"adv", name:"Adverbio", common:true, visible:true}]
            }
            return array.concat(b);
        }();

        var verbValue = function(){
            if ($scope.interfaceLanguageId == 1){//CAT
               return 'Verb';
            }else if ($scope.interfaceLanguageId == 2){
                return 'Verbo';
            }
        };

        $scope.checkVerbCDP1 = function (type){
            var typeBenef = $scope.Pattern1.Beneficiary.type;
            var typeLocto = $scope.Pattern1.Locto.type;
            if(type === "verb") {
                $scope.BenefOptionsPattern1[24] = {value:"verb", name: verbValue(), common:true, visible:false};
                $scope.LocatOptionsPattern1[24] = {value:"verb", name: verbValue(), common:true, visible:false};
            }else{
                if(typeBenef !== "verb" && typeLocto !== "verb"){
                    $scope.BenefOptionsPattern1[24] = {value:"verb", name: verbValue(), common:true, visible:true};
                }if(typeLocto !== "verb" && typeBenef !== "verb"){
                    $scope.LocatOptionsPattern1[24] = {value:"verb", name: verbValue(), common:true, visible:true};
                }
            }
        };

        $scope.checkVerbCDP2 = function (type){
            var typeBenef = $scope.Pattern2.Beneficiary.type;
            var typeLocto = $scope.Pattern2.Locto.type;
            if(type === "verb") {
                $scope.BenefOptionsPattern2[24] = {value:"verb", name: verbValue(), common:true, visible:false};
                $scope.LocatOptionsPattern2[24] = {value:"verb", name: verbValue(), common:true, visible:false};
            }else{
                if(typeBenef !== "verb" && typeLocto !== "verb"){
                    $scope.BenefOptionsPattern2[24] = {value:"verb", name: verbValue(), common:true, visible:true};
                }if(typeLocto !== "verb" && typeBenef !== "verb"){
                    $scope.LocatOptionsPattern2[24] = {value:"verb", name: verbValue(), common:true, visible:true};
                }
            }
        };

        $scope.checkVerbBenefP1 = function(type){
            var typeCD = $scope.Pattern1.CD.type;
            var typeLocto = $scope.Pattern1.Locto.type;
            if(type === "verb" ) {
                $scope.CDOptionsPattern1[24] = {value:"verb", name: verbValue(), common:true, visible:false};
                $scope.LocatOptionsPattern1[24] = {value:"verb", name: verbValue(), common:true, visible:false};
            }else{
                if(typeCD !== "verb" && typeLocto !== "verb"){
                    $scope.CDOptionsPattern1[24] = {value:"verb", name: verbValue(), common:true, visible:true};
                }if (typeLocto !== "verb" && typeCD !== "verb"){
                    $scope.LocatOptionsPattern1[24] = {value:"verb", name: verbValue(), common:true, visible:true};
                }
            }
        };

        $scope.checkVerbBenefP2 = function(type){
            var typeCD = $scope.Pattern2.CD.type;
            var typeLocto = $scope.Pattern2.Locto.type;
            if(type === "verb" ) {
                $scope.CDOptionsPattern2[24] = {value:"verb", name: verbValue(), common:true, visible:false};
                $scope.LocatOptionsPattern2[24] = {value:"verb", name: verbValue(), common:true, visible:false};
            }else{
                if(typeCD !== "verb" && typeLocto !== "verb"){
                    $scope.CDOptionsPattern2[24] = {value:"verb", name: verbValue(), common:true, visible:true};
                }if (typeLocto !== "verb" && typeCD !== "verb"){
                    $scope.LocatOptionsPattern2[24] = {value:"verb", name: verbValue(), common:true, visible:true};
                }
            }
        };

        $scope.checkVerbLoctoP1 = function (type){
            var typeCD = $scope.Pattern1.CD.type;
            var typeBenef = $scope.Pattern1.Beneficiary.type;
            if(type === "verb") {
                $scope.BenefOptionsPattern1[24] = {value:"verb", name: verbValue(), common:true, visible:false};
                $scope.CDOptionsPattern1[24] = {value:"verb", name: verbValue(), common:true, visible:false};
            }else{
                if(typeBenef !== "verb" && typeCD !== "verb"){
                    $scope.BenefOptionsPattern1[24] = {value:"verb", name: verbValue(), common:true, visible:true};
                }if(typeCD !== "verb" && typeBenef !== "verb"){
                    $scope.CDOptionsPattern1[24] = {value:"verb", name: verbValue(), common:true, visible:true};
                }
            }
        };

        $scope.checkVerbLoctoP2 = function (type){
            var typeCD = $scope.Pattern2.CD.type;
            var typeBenef = $scope.Pattern2.Beneficiary.type;
            if(type === "verb") {
                $scope.BenefOptionsPattern2[24] = {value:"verb", name: verbValue(), common:true, visible:false};
                $scope.CDOptionsPattern2[24] = {value:"verb", name: verbValue(), common:true, visible:false};
            }else{
                if(typeBenef !== "verb" && typeCD !== "verb"){
                    $scope.BenefOptionsPattern2[24] = {value:"verb", name: verbValue(), common:true, visible:true};
                }if(typeCD !== "verb" && typeBenef !== "verb"){
                    $scope.CDOptionsPattern2[24] = {value:"verb", name: verbValue(), common:true, visible:true};
                }
            }
        };

        $scope.showComplement = function(showPattern, id){
          switch(id) {
              case 1:
                  showPattern.CD = true;
                  $scope.$broadcast('rebuild:verbPatternScrollbar');
                  break;
              case 2:
                  showPattern.Receiver = true;
                  $scope.$broadcast('rebuild:verbPatternScrollbar');
                  break;
              case 3:
                  showPattern.Beneficiary = true;
                  $scope.$broadcast('rebuild:verbPatternScrollbar');
                  break;
              case 4:
                  showPattern.Acomp = true;
                  $scope.$broadcast('rebuild:verbPatternScrollbar');
                  break;
              case 5:
                  showPattern.Tool = true;
                  $scope.$broadcast('rebuild:verbPatternScrollbar');
                  break;
              case 6:
                  showPattern.Modo = true;
                  $scope.$broadcast('rebuild:verbPatternScrollbar');
                  break;
              case 7:
                  showPattern.Locto = true;
                  $scope.$broadcast('rebuild:verbPatternScrollbar');
                  break;
          }
        };

        $scope.cleanPattern = function (showPattern, Pattern, property){
            switch(property) {
                case 'CD':
                    showPattern.CD = false;
                    Pattern.CD = {'priority': 0, 'type':"", 'preposition':""};
                    $scope.$broadcast('rebuild:verbPatternScrollbar');
                    break;
                case 'Receiver':
                    showPattern.Receiver = false;
                    Pattern.Receiver = {'priority':0, 'preposition':""};
                    $scope.$broadcast('rebuild:verbPatternScrollbar');
                    break;
                case 'Beneficiary':
                    showPattern.Beneficiary = false;
                    Pattern.Beneficiary = {'priority':0, 'type':"", 'preposition':""};
                    $scope.$broadcast('rebuild:verbPatternScrollbar');
                    break;
                case 'Acomp':
                    showPattern.Acomp = false;
                    Pattern.Acomp = {'priority':0, 'preposition':""};
                    $scope.$broadcast('rebuild:verbPatternScrollbar');
                    break;
                case 'Tool':
                    showPattern.Tool = false;
                    Pattern.Tool = {'priority':0, 'preposition':""};
                    $scope.$broadcast('rebuild:verbPatternScrollbar');
                    break;
                case 'Modo':
                    showPattern.Modo = false;
                    Pattern.Modo = {'priority':0, 'preposition':""};
                    $scope.$broadcast('rebuild:verbPatternScrollbar');
                    break;
                case 'Locto':
                    showPattern.Locto = false;
                    Pattern.Locto = {'priority': 0, 'type':"", 'preposition':""};
                    $scope.$broadcast('rebuild:verbPatternScrollbar');
                    break;
            }
        };

        //Guardado
        $scope.addVerbErrors = {'verb': false, 'imgVerb': false };
        $scope.addVerbErrorsP1 = {'Patron': false, 'CD': false, 'Receiver': false, 'Beneficiary': false, 'Acomp': false, 'Tool': false, 'Modo':false, 'Tool': false, 'Locto': false};
        $scope.addVerbErrorsP2 = {'Patron': false, 'CD': false, 'Receiver': false, 'Beneficiary': false, 'Acomp': false, 'Tool': false, 'Modo':false, 'Tool': false, 'Locto': false};
        $scope.conjugationsErrors = {'Presente': false, 'Imperfecto': false,  'Pasado': false, 'Futuro': false, 'Prsubj': false, 'Impsubj': false, 'Imperativo': false, 'NoPersonales': false};
        $scope.isEdit = false;
        $scope.verbID = false;

        $scope.editMode = function(){
            $scope.isEdit = $rootScope.addWordparam.newmod;
            $scope.verbID = $rootScope.addWordparam.type;
           if ($scope.isEdit === true){
               $scope.imgHasBeenUploaded = true;
               var URL = $scope.baseurl + "AddVerb/getAllData";
               var postdata = {verbID : $scope.verbID};
               $http.post(URL, postdata).success(function (response) {
                   console.log(response);
                   $scope.conjugations = response.conjugations;
                   setVerbInfo(response.verbText, response.imgPicto, response.imgFolder, response.pronominal);
                   setConjugations();
                   setPattern('Pattern1', response.patterns[0]);
                   if(response.patterns.length > 1){
                       setPattern('Pattern2', response.patterns[1]);
                   }
                   $scope.$broadcast('rebuild:verbPatternScrollbar');
               });
           }
        };

        function setVerbInfo(verbName, imgPicto, imgFolder, pronominal){
            $scope.verb.name = verbName;
            $scope.imgPicto.value = imgPicto;
            $scope.imgFolder.value = imgFolder;
            if(pronominal === '1'){
                $scope.pronominal.value = 1;
            }else if (pronominal === '0'){
                $scope.pronominal.value = 0;
            }
        }

        function setPattern(pattern, data){
            console.log(data);
            if(pattern === 'Pattern1'){
                $scope.Pattern1 = {
                    'Patron': {'pronominal': data.pronominal, 'subj': data.subj, 'subjdef': data.subjdef, 'defaulttense': data.defaulttense, 'exemple': data.exemple},
                    'CD': {'priority': data.theme === '' ? 0 : data.theme, 'type': data.themetipus, 'preposition': data.themeprep, 'themedef': data.themedef},
                    'Receiver': {'priority': data.receiver === '' ? 0 : data.receiver, 'preposition': data.receiverprep, 'receiverdef': data.receiverdef},
                    'Beneficiary': {'priority': data.benef === '' ? 0 : data.benef, 'type':  data.beneftipus, 'preposition': data.benefprep},
                    'Acomp': {'priority': data.acomp === '' ? 0 : data.acomp, 'preposition': data.acompprep},
                    'Tool': {'priority': data.tool === '' ? 0 : data.tool, 'preposition': data.toolprep},
                    'Modo': {'priority': data.manera === '' ? 0 : data.manera, 'type': data.maneratipus},
                    'Locto': {'priority': data.locto === '' ? 0 : data.locto, 'type': data.loctotipus, 'preposition': data.loctoprep}
                };
                showEditPattern($scope.Pattern1, $scope.showPattern1);
                $scope.verbPattern1 = true;
            }else if (pattern === 'Pattern2'){
                $scope.Pattern2 = {
                    'Patron': {'pronominal': data.pronominal, 'subj': data.subj, 'subjdef': data.subjdef, 'defaulttense': data.defaulttense, 'exemple': data.exemple},
                    'CD': {'priority': data.theme === '' ? 0 : data.theme, 'type': data.themetipus, 'preposition': data.themeprep, 'themedef': data.themedef},
                    'Receiver': {'priority': data.receiver === '' ? 0 : data.receiver, 'preposition': data.receiverprep, 'receiverdef': data.receiverdef},
                    'Beneficiary': {'priority': data.benef === '' ? 0 : data.benef, 'type':  data.beneftipus, 'preposition': data.benefprep},
                    'Acomp': {'priority': data.acomp === '' ? 0 : data.acomp, 'preposition': data.acompprep},
                    'Tool': {'priority': data.tool === '' ? 0 : data.tool, 'preposition': data.toolprep},
                    'Modo': {'priority': data.manera === '' ? 0 : data.manera, 'type': data.maneratipus},
                    'Locto': {'priority': data.locto === '' ? 0 : data.locto, 'type': data.loctotipus, 'preposition': data.loctoprep}
                };
                showEditPattern($scope.Pattern2, $scope.showPattern2);
                $scope.showVerbPattern2 = true;
                $scope.verbPattern2 = true;
            }
            console.log("PATRÓN 1: ");
            console.log($scope.Pattern1);
            console.log("PATRÓN 2: ");
            console.log($scope.Pattern2);
        }

        function showEditPattern(Pattern, showPattern){
                if (Pattern.CD.priority !== 0){
                    showPattern.CD = true;
                }
                if (Pattern.Receiver.priority !== 0){
                    showPattern.Receiver = true;
                }
                if (Pattern.Beneficiary.priority !== 0){
                    showPattern.Beneficiary = true;
                }
                if (Pattern.Acomp.priority !== 0){
                    showPattern.Acomp = true;
                }
                if (Pattern.Tool.priority !== 0){
                    showPattern.Tool = true;
                }
                if (Pattern.Modo.priority !== 0){
                    showPattern.Modo = true;
                }
            if (Pattern.Locto.priority !== 0){
                showPattern.Locto = true;
            }

        }

        function checkVerbErrors(addVerbErrors){
            var verb = $scope.verb.name;
            if(verb === ''){
                addVerbErrors.verb = true;
            }else {
                addVerbErrors.verb = false;
            }
            return addVerbErrors.verb;
        };
        function checkImgVerbErrors(addVerbErrors){
            var imgPicto = $scope.imgPicto.value;
            if(imgPicto === 'arrow-question.png' || imgPicto === null){
                addVerbErrors.imgVerb = true;
            }else{
                addVerbErrors.imgVerb = false;
            }
            return addVerbErrors.imgVerb;
        };

        function checkErrorsConjugations(){
            $scope.conjugationsErrors.Presente = checkConjugation($scope.presente);
            $scope.conjugationsErrors.Imperfecto = checkConjugation($scope.imperfecto);
            if($scope.interfaceLanguageId == 2) {//ESP
                $scope.conjugationsErrors.Pasado = checkConjugation($scope.pasado);
            }
            $scope.conjugationsErrors.Futuro = checkConjugation($scope.futuro);
            $scope.conjugationsErrors.Prsubj = checkConjugation($scope.prsubj);
            $scope.conjugationsErrors.Impsubj = checkConjugation($scope.impsubj);
            $scope.conjugationsErrors.Imperativo = checkImperativoConjugation($scope.imperativo);
            $scope.conjugationsErrors.NoPersonales = checkFormasNoPersonales($scope.formasNoPersonales);
            var errors = $scope.conjugationsErrors;
            if( errors.Presente == false && errors.Imperfecto == false &&
                errors.Pasado == false && errors.Futuro == false &&
                errors.Prsubj == false && errors.Impsubj == false &&
                errors.Imperativo == false && errors.NoPersonales == false){
                //NO ERRORS
                return false;
            }else {
                return true;
            }
        };
        function checkConjugation(Conjugation){
            if (Conjugation.persona.ps1 == '' || Conjugation.persona.ps2 == '' ||
                Conjugation.persona.ps3 == '' || Conjugation.persona.pp1 == '' ||
                Conjugation.persona.pp2 == '' || Conjugation.persona.pp3 == '' ||
                Conjugation.persona.ps1 == null || Conjugation.persona.ps2 == null ||
                Conjugation.persona.ps3 == null || Conjugation.persona.pp1 == null ||
                Conjugation.persona.pp2 == null || Conjugation.persona.pp3 == null){
                return true;
            }
            else{
                return false;
            }
        };
        function checkImperativoConjugation(Conjugation){
            if (Conjugation.persona.ps2 == '' || Conjugation.persona.ps3 == '' ||
                Conjugation.persona.pp1 == '' || Conjugation.persona.pp2 == '' ||
                Conjugation.persona.pp3 == '' || Conjugation.persona.ps2 == null ||
                Conjugation.persona.ps3 == null || Conjugation.persona.pp1 == null ||
                Conjugation.persona.pp2 == null || Conjugation.persona.pp3 == null){
                return true;
            }else{
                return false;
            }
        };
        function checkFormasNoPersonales(Conjugation){
            if (Conjugation.infinitivo == '' || Conjugation.gerundio == '' || Conjugation.participio == '' ||
                    Conjugation.infinitivo == null || Conjugation.gerundio == null || Conjugation.participio == null){
                return true;
            }else {
                return false;
            }
        };
        function checkErrorsPattern(Pattern, showPattern, patternErrors){
            var errors = false;
            if(Pattern.Patron.subj === '' || Pattern.Patron.subjdef === '' || Pattern.Patron.defaulttense === '' ){
                errors = true;
                patternErrors.Patron = true;
            }else {
                patternErrors.Patron = false;
            }
            if (showPattern.CD === true){
                if (Pattern.CD.priority === 0 || Pattern.CD.type === '' || Pattern.CD.type === null){
                    errors = true;
                    patternErrors.CD = true;
                }else {
                    patternErrors.CD = false;
                }
            }
            if (showPattern.Receiver === true){
                if (Pattern.Receiver.priority === 0){
                    errors = true;
                    patternErrors.Receiver = true;
                }else {
                    patternErrors.Receiver = false;
                }
            }
            if (showPattern.Beneficiary === true){
                if (Pattern.Beneficiary.priority === 0 || Pattern.Beneficiary.type === '' || Pattern.Beneficiary.type === null){
                    errors = true;
                    patternErrors.Beneficiary = true;
                }else {
                    patternErrors.Beneficiary = false;
                }
            }
            if (showPattern.Acomp === true){
                if (Pattern.Acomp.priority === 0){
                    errors = true;
                    patternErrors.Acomp = true;
                }else {
                    patternErrors.Acomp = false;
                }
            }
            if (showPattern.Tool === true){
                if (Pattern.Tool.priority === 0){
                    errors = true;
                    patternErrors.Tool = true;
                }else {
                    patternErrors.Tool = false;
                }
            }
            if (showPattern.Modo === true){
                if (Pattern.Modo.priority === 0 || Pattern.Modo.type === '' || Pattern.Modo.type === null){
                    errors = true;
                    patternErrors.Modo = true;
                }else {
                    patternErrors.Modo = false;
                }
            }
            if (showPattern.Locto === true){
                if (Pattern.Locto.priority === 0 || Pattern.Locto.type === '' || Pattern.Locto.type === null){
                    errors = true;
                    patternErrors.Locto = true;
                }else {
                    patternErrors.Locto = false;
                }
            }
            return errors;
        };

        $scope.addVerb = function(){
                if(!checkVerbErrors($scope.addVerbErrors) && !checkErrorsConjugations()){
                    var URL = $scope.baseurl + "AddVerb/verbExist";
                    var postdata = {verb : $scope.verb.name};
                    $http.post(URL, postdata).success(function (response) {
                        if(response === true && $scope.isEdit !== true){
                            var textBD = $scope.content;
                            $scope.infoModalContent = textBD.modalVerbExists;
                            $scope.infoModalTitle = textBD.modalInfoTitle;
                            $scope.style_changes_title = 'padding-top: 2vh;';
                            $('#infoModal').modal('toggle');
                        }else{
                            if(!checkImgVerbErrors($scope.addVerbErrors) && !checkErrorsPattern($scope.Pattern1, $scope.showPattern1, $scope.addVerbErrorsP1)){
                                if($scope.verbPattern2 === true){
                                    if(!checkErrorsPattern($scope.Pattern2, $scope.showPattern2, $scope.addVerbErrorsP2)){
                                        var patterns = [$scope.Pattern1, $scope.Pattern2];
                                    }
                                    else {
                                        return;
                                    }
                                }else{
                                    var patterns = [$scope.Pattern1];
                                }
                                var conjugations = {presente: $scope.presente, imperfecto: $scope.imperfecto,
                                                    pasado: $scope.pasado, futuro: $scope.futuro, prsubj: $scope.prsubj, impsubj: $scope.impsubj, imperativo: $scope.imperativo,
                                                    formasNoPersonales: $scope.formasNoPersonales};

                                var URL = $scope.baseurl + "AddVerb/insertData";
                                var postdata = {isEdit: $scope.isEdit, verbID: $scope.verbID, img: $scope.imgPicto.value, folder: $scope.imgFolder.value, verb: $scope.verb.name, pronominal: $scope.pronominal.value, conjugations: conjugations, patterns: patterns};
                                console.log(postdata);
                                $http.post(URL, postdata).success(function (response){
                                    console.log(response);
                                    $location.path("/panelGroups");
                                });

                            }
                        }
                    });
                }
        };
        $scope.EditWordRemove = function () {
            var postdata = {id: $scope.verbID, type: 'verb'};
            console.log(postdata);
            var URL = $scope.baseurl + "AddWord/EditWordRemove";
            $http.post(URL, postdata).success(function (response){
                $location.path("/panelGroups");
            });
        };
        
        // Open popup to search personal images
            $scope.popUpSearchImgs = function(){
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
                var postdata = {name: name};
                $http.post(url, postdata).
                success(function (response)
                {
                    $scope.imgData = response.data;
                    // console.log(response.data);
                });
             };
             
             $scope.selectImage = function (path) {
                 aux = path.split("/");
                 $scope.imgFolder.value = aux[0] + "/" + aux[1] + "/";
                 $scope.imgPicto.value = aux[2];
                 $scope.imgHasBeenUploaded = true;
                 $('#imgsSearchModal').modal('hide'); //Hide modal
             };
        
    });

