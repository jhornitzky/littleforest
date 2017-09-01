<html>
<head>
    <title>greensquare - vege gardening made simple</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/app.css">
</head>
<body>
    <section class="app">
      <header class="header" style="padding-left:6px">
        <h1>green square</h1>
      </header>
      <section class="main container-fluid">
          <div class="row">
              <div class="col-3" v-for="(square,key) in squares">
                  <div class="square">
                      {{square.name}}
                  </div>
              </div>
              <div class="col-3">
                  <div class="square add-new" @click="addNewSquare()">
                      + ADD
                  </div>
              </div>
          </div>
      </section>
    </section>
    <footer class="info">
    </footer>

    <!-- scripts -->
    <script src="js/jquery.js"></script>
    <!--<script src="js/bootstrap.js"></script>-->
    <script src="js/vue.js"></script>
    <script src="js/vue-localstorage.js"></script>
    <script src="js/vue-draggable.js"></script>
    <script type="text/javascript">
        // localStorage persistence
        var STORAGE_KEY = 'greensquare'
        var squareStorage = {
          fetch: function () {
            var squares = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]')
            squares.forEach(function (square, index) {
              square.id = index
            })
            squareStorage.uid = squares.length
            return squares
          },
          save: function (squares) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(squares))
          }
        }

        //app
        var app = new Vue({
          // app initial state
          data: {
            chart: [
                {name:"tomato",maturityWeeks:"12"},
                {name:"spinach",maturityWeeks:"11"},
                {name:"lettuce",maturityWeeks:"10"},
            ],
            squares: [
                {name:"tomato",planted:"2017-04-17"},
                {name:"spinach",planted:"2017-04-17"},
                {name:"lettuce",planted:"2017-04-17"}
            ],
            //squares: squareStorage.fetch(),
            newsquare: '',
            editedsquare: null,
            visibility: 'all',
            focusKey:-1
          },

          // watch squares change for localStorage persistence
          watch: {
            squares: {
              handler: function (squares) {
                squareStorage.save(squares)
              },
              deep: true
            }
          },

          // computed properties
          // http://vuejs.org/guide/computed.html
          computed: {
            filteredsquares: function () {
              return this.squares
            },
          },

          methods: {
            addsquare: function () {
              var value = this.newsquare && this.newsquare.trim()
              if (!value) {
                return
              }
              this.newsquare = ''
              this.squares.splice(0, 0,{
                id: squareStorage.uid++,
                title: value,
                completed: false
              })
            },

            removesquare: function (square) {
              this.squares.splice(this.squares.indexOf(square), 1)
            },

            editsquare: function (square) {
              this.beforeEditCache = square.title
              this.editedsquare = square
            },

            doneEdit: function (square) {
              square.title = square.title.trim()
            },

            checkForDelete: function (square) {
              square.title = square.title.trim()
              if (!square.title) {
                var i = this.squares.indexOf(square)
                this.removesquare(square)
                if (i > 0 && i < this.squares.length-1) this.focusNextTick(i)
                else if (i > 0 && i >= this.squares.length-1) this.focusNextTick(i-1)
              }
            }

          },

          // a custom directive to wait for the DOM to be updated
          // before focusing on the input field.
          // http://vuejs.org/guide/custom-directive.html
          directives: {
            'square-focus': function (el, binding) {
              if (binding.value) el.focus()
            }
          }
        });

        app.$mount('.app')
    </script>
</body>
</html>
