$(document).ready(function() {
	var exampleBarChartData = {
		"datasets": {
			"values": [50, 100, 300, 500, 200,52,720, 60, 24, 995, 225, 112],
			"labels": [
				"Jan", 
				"Feb", 
				"Mar", 
				"Apr", 
				"May",
				"Jun",
				"Jul",
				"Aug",
				"Sep",
				"Oct",
				"Nov",
				"Dec"
			],
			"color": "blue"
		},
		"title": "&nbsp;",
		"noY": true,
		"height": "300px",
		"width": "500px",
		"background": "#FFFFFF",
		"shadowDepth": "1"
	};

	MaterialCharts.bar("#bar-chart-example", exampleBarChartData)

	var examplePieChartData = {
		"dataset": {
			"values": [5, 30, 5, 20, 40],
			"labels": [
				"Jan", 
				"Feb", 
				"Mar", 
				"Apr", 
				"May",
				"Jun",
				"Jul",
				"Aug",
				"Sep",
				"Oct",
				"Nov",
				"Dec"
			],
		},
		"title": "Example Pie Chart",
		"height": "300px",
		"width": "500px",
		"background": "#FFFFFF",
		"shadowDepth": "1"
	};

	MaterialCharts.pie("#pie-chart-example", examplePieChartData)
});