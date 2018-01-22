const viewURL = 'scripts/view.php'

var info = new Vue({

	el: '#viewer',

	data: {
		results: null,
		fields1: null,
		fields2: null
	},

	created: function () {
		this.fetchData()
	},

	methods: {
		fetchData: function () {

			var xhr = new XMLHttpRequest()
			var self = this
			xhr.open('GET', viewURL)
			xhr.onload = function () {
				let results = JSON.parse(xhr.responseText)
				self.fields1 = results.fields1
				self.fields2 = results.fields2
			}
			xhr.send()
		}
	}
})