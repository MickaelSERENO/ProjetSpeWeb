// js/todoList.js
//'use strict';



/**
 * Déclaration de l'application demoApp
 */
/*var demoApp = angular.module('demoApp', [
    // Dépendances du "module"
    'todoList', 'myApp'
]);*/

/**
 * Déclaration du module todoList
 */
//var todoList = angular.module('todoList',[]);

/**
 * Contrôleur de l'application "Todo List" décrite dans le chapitre "La logique d'AngularJS".
 */
/*todoList.controller('todoCtrl', ['$scope',
    function ($scope) {

        // Pour manipuler plus simplement les todos au sein du contrôleur
        // On initialise les todos avec un tableau vide : []
        var todos = $scope.todos = [];
		
	// Ajouter un todo
	$scope.addTodo = function () {
		// .trim() permet de supprimer les espaces inutiles
		// en début et fin d'une chaîne de caractères
		var newTodo = $scope.newTodo.trim();
		if (!newTodo.length) {
			// éviter les todos vides
			return;
		}
		todos.push({
			// on ajoute le todo au tableau des todos
			title: newTodo,
			completed: false
		});
		// Réinitialisation de la variable newTodo
		$scope.newTodo = '';
	};*/
	
	// Enlever un todo
	/*$scope.removeTodo = function (todo) {
		todos.splice(todos.indexOf(todo), 1);
	};*/

	// Cocher / Décocher tous les todos
	/*$scope.markAll = function (completed) {
		todos.forEach(function (todo) {
			todo.completed = completed;
		});
	};

	// Enlever tous les todos cochés
	$scope.clearCompletedTodos = function () {
		$scope.todos = todos = todos.filter(function (todo) {
			return !todo.completed;
		});
	};
	}
]);


var myApp = angular.module('myApp', []);

myApp.controller('ColorCtrl', ['$scope', function($scope){
	$scope.customStyle = {};
	$scope.color = function (){
		//what to do here?
		$scope.customStyle.style = {"background-color":"green", "color":"white"};
	}

}]);*/

var isClicked;
var mouseX;
var mouseY;
var ctx;
var canvas;

function clearCanvas()
{
	ctx.save();
	ctx.setTransform(1, 0, 0, 1, 0, 0);
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	ctx.restore();
	ctx.fillStyle = "orange";
	ctx.fillText("Hello", 50, 50);
	ctx.fillText("World", 130, 50);
}

function onClickCanvas($event)
{
	isClicked = 1;
	mouseX = $event.offsetX;
	mouseY = $event.offsetY;
}

function onMouseMove($event)
{
	if(isClicked == 1)
	{
		clearCanvas();
		ctx.fillStyle = "rgba(0,0,255,0.3)";
		ctx.fillRect(mouseX, mouseY, $event.offsetX-mouseX, $event.offsetY-mouseY);
	}
}

function onMouseUpCanvas($event)
{
	clearCanvas();
	isClicked = 0;

}

var myApp = angular.module("AppGame2", []);
myApp.controller("CanvasController", function($scope)
{
	$scope.onClickCanvas   = onClickCanvas;
	$scope.onMouseMove = onMouseMove;
	$scope.onMouseUpCanvas = onMouseUpCanvas;
	
});

window.onload = function()
{
	isClicked = 0;
	canvas   = document.getElementById('canvasJeu2');
	ctx      = canvas.getContext('2d');
	
	ctx.font = "25px Arial";
	ctx.fillStyle = "orange";
	ctx.fillText("Hello", 50, 50);
	ctx.fillText("World", 130, 50);
	
}