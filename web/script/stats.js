var myApp = angular.module("statsApp", []);

function onRowStudentClick(id, $event)
{
    window.location.href = "studentCaracteristics.php?studentID=" + encodeURIComponent(id);
}

function onRowHistoricClick(id, $event)
{
    console.log("onClickHistoric, id : " + id);
    window.location.href = window.location.hostname + "historicCaracteristics.php?historicID=" + encodeURIComponent(id);
}

myApp.controller("listStatsCtrl", function($scope)
{
    $scope.onRowStudentClick = onRowStudentClick;
    $scope.onRowHistoricClick = onRowHistoricClick;
	$scope.studentForm = {}
	$scope.studentRow = []
	$scope.issue = "";

	$scope.addStudent = function()
	{
		if($scope.studentRow.firstName == null || $scope.studentRow.name == null || $scope.studentRow.password == null)
			return;
		//Send student to the database
		var httpCtx = new XMLHttpRequest();

		httpCtx.onreadystatechange = function()
		{
			if(httpCtx.readyState == 4 && (httpCtx.status == 200 || httpCtx.status == 0))
			{
				console.log(httpCtx.responseText+"okep");
				if(httpCtx.responseText == 1)
				{
					$scope.studentRow.push({'firstName':$scope.studentForm.firstName, 'name':$scope.studentForm.name});
					$scope.issue = "L'étudiant "+$scope.studentForm.firstName + " " + $scope.studentForm.name + " à été ajouté";
					$scope.$apply();
				}

				else if(httpCtx.responseText == -2)
				{
					$scope.issue = "Mot de passe trop court";
					$scope.$apply();
				}

				else if(httpCtx.responseText == 0)
				{
					console.log("yeah, 0");
					$scope.issue = "L'étudiant "+$scope.studentForm.firstName + " " + $scope.studentForm.name + " existe déjà.";
					$scope.$apply();
				}

				else
				{
				}
			}
		}
		httpCtx.open("POST", "/ClientQuery/addStudent.php", true);
		httpCtx.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		httpCtx.send("nameStudent="+$scope.studentForm.name+"&surnameStudent="+$scope.studentForm.firstName+"&password="+$scope.studentForm.password);
		
	}
});

myApp.directive("myStatsaccordion", function()
{
    return{
        restrict: 'EA',
        replace: true,
		scope      : {height: '@height'},
        transclude: true,
        template: '<div class="statsAccordion" ng-transclude></div>',
        controller: function(){
			var tabIndice = -1;
			var contentsIndice = -1;
			var contents  = [];
			var titles = [];

            this.open = function(tabIndiceTitle)
			{
				titles.forEach(function(title)
				{
					title.font ="normal";
				});
				titles[tabIndiceTitle].font="bold";

				contents.forEach(function(content)
				{
					content.show = false;
				});
				contents[tabIndiceTitle].show = true;
            };

            this.addTitle = function(title)
			{
				titles.push(title);
				tabIndice++;
				return tabIndice;
            };

            this.addContent = function(content)
			{
                contents.push(content);
				contentsIndice++;
				if(contentsIndice == 0)
					content.show = true;

				this.open(0);
				return contentsIndice;
            };
        }
    };
});

myApp.directive("myStattabitem", function()
{
	return{
		restrict   : 'EA',
		replace    : true,
		require    : '^myStatsaccordion',
		scope      : {title: '@title'},
		template   : '<div display="flex" ng-click="toggleMe()" class="tabTitle" style="font-weight:{{font}}">{{title}}</div>',
		link       : function($scope, element, attrs, accordionCtrl){
			$scope.font = "normal";
			var indice = accordionCtrl.addTitle($scope);
			$scope.toggleMe = function()
			{
				$scope.font="bold";
				accordionCtrl.open(indice);
			};
		}
	};
});

myApp.directive("myStattabcontent", function()
{
	return{
		restrict   : 'EA',
		replace : true,
		transclude : true,
		scope      : {},
		require    : '^myStatsaccordion',
		template   : '<div ng-show="show" class="tabContent" ng-transclude></div>',
		link       : function($scope, element, attrs, accordionCtrl){
			$scope.show=false;
			accordionCtrl.addContent($scope);
		}
	};
});

function onLoadFunction()
{
	setSettingsSize();
}


window.onload = onLoadFunction;
