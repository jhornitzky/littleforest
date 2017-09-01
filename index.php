<!DOCTYPE html>
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
                <div class="col-2" v-for="n in 36"><!-- FIXME make the max adjustable to increase plot size -->
                    <div :data-itemid="n" class="holder" droppable="true" v-on:drop="drop" v-on:dragover="dragover">
                        <div class="square" v-if="getsquare(n)" draggable="true" v-on:dragstart="tempdragsquare = getsquare(n)">
                            {{getsquare(n).name}}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <footer class="info">
    </footer>

    <!-- scripts -->
    <!--<script src="js/jquery.js"></script>
    <script src="js/bootstrap.js"></script>-->
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
                {name:"tomato",maturityWeeks:"12",bestgrown:"jan-mar"},
                {name:"spinach",maturityWeeks:"11",bestgrown:"may-jun"},
                {name:"lettuce",maturityWeeks:"10",bestgrown:"oct-dec"},
            ],
            squares: [
                {slot:1,name:"tomato",planted:"2017-04-17"},
                {slot:5,name:"spinach",planted:"2017-04-17"},
                {slot:10,name:"lettuce",planted:"2017-04-17"}
            ],
            tempdragsquare:null,
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
            }
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

            getsquare:function(n) {
                for (s in this.squares) {
                    var sq = this.squares[s];
                    if (sq.slot == n) return sq;
                }
                return false;
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
            },

            drop: function(e) {
                this.tempdragsquare.slot = e.target.getAttribute('data-itemid');
                //allow draggable...
            },

            dragover: function(e) {
                e.preventDefault(); //need to prevent default for draggable to work
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
