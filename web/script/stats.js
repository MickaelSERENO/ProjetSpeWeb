var myApp = angular.module("statsApp", []);
myApp.controller("listStatsCtrl", function($scope)
{
});

myApp.directive("myStatsaccordion", function()
{
    return{
        restrict: 'EA',
        replace: true,
        transclude: true,
        template: '<div class="statsAccordion" ng-transclude></div>',
        controller: function(){
			var tabIndice = -1;
			var contentsIndice = -1;
			var contents  = [];

            this.open = function(tabIndiceTitle)
			{
				contents.forEach(function(content)
				{
					content.show = false;
				});
				contents[tabIndiceTitle].show = true;
            };

            this.addTitle = function(title)
			{
				tabIndice++;
				return tabIndice;
            };

            this.addContent = function(content)
			{
                contents.push(content);
				contentsIndice++;
				if(contentsIndice == 0)
					content.show = true;
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
		template   : '<div ng-click="toggleMe()" class="tabTitle">{{title}}</div>',
		link       : function($scope, element, attrs, accordionCtrl){
			var indice = accordionCtrl.addTitle();
			$scope.toggleMe = function()
			{
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
