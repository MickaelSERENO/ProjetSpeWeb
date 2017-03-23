var myApp = angular.module("statsApp", []);
myApp.controller("listStatsCtrl", function($scope)
{
});

myApp.directive("statsAccordion", function()
{
    return{
        restrict: 'EA',
        replace: true,
        transclude: true,
        template: '<ul class="statsAccordion" ng-transclude></ul>',
        controller: function(){
            var expanders = [];
            this.open = function(selectedExpander){
                expanders.forEach(function(expander){
                    if(selectedExpander != expander){
                        expander.show = false;
                    }
					expander.show = true;
                });
            };
            this.addExpander = function(expander){
                expanders.push(expander);
            };
        }
    };
});

myApp.directive("statsExpander", function()
{
	return{
		restrict  : 'A',
		transcule : true,
		require   : '^?statsAccordion',
		scope     : {
			title: '@title' 
		},
		templates : '<li ng-click="toggleMe()" class="statsExpander">{{title}}</li>' +
					'<div ng-transcule ng-show=show></div>',
		link      : function($scope, element, attrs, accordionCtrl){
			accordionCtrl.addExpander(this);
			$scope.toggleMe = function(){
				accordionCtrl.open(this);
			};
		}
	};
});
