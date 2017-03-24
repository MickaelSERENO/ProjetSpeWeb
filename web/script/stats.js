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
        template: '<ul class="statsAccordion" ng-transclude></ul>',
        controller: function(){
            var expanders = [];
            this.open = function(selectedExpander)
			{
                expanders.forEach(function(expander)
				{
					expander.show = false;
                });
				selectedExpander.show = true;
            };
            this.addExpander = function(expander)
			{
                expanders.push(expander);
            };
        }
    };
});

myApp.directive("myStatsexpander", function()
{
	return{
		restrict   : 'EA',
		replace    : true,
		transclude : true,
		require    : '^?myStatsaccordion',
		scope      : {title: '@title'},
		template   : '<div>' + 
					     '<li  ng-click="toggleMe()" class="statsExpander">{{title}}</li>' +
					     '<div ng-show="show" ng-transclude></div>'+
					 '</div>',
		link       : function($scope, element, attrs, accordionCtrl){
			accordionCtrl.addExpander($scope);
			$scope.show = false;
			$scope.toggleMe = function()
			{
				accordionCtrl.open($scope);
			};
		}
	};
});
